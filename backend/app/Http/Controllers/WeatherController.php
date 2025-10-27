<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Spatie\Async\Pool;
use App\Models\Weather;

class WeatherController extends Controller
{
    public function getWeather()
    {
        // ✅ List of 15 cities
        $cities = [
            'Colombo', 'Tokyo', 'Seoul', 'Paris', 'New York',
            'London', 'Dubai', 'Sydney', 'Moscow', 'Mumbai',
            'Singapore', 'Rome', 'Beijing', 'Berlin', 'Toronto'
        ];

        $apiKey = env('OPENWEATHER_API_KEY');

        $pool = Pool::create();

        foreach ($cities as $city) {
            $pool->add(function () use ($city, $apiKey) {
                // Step 1: Check if cached data exists (last 10 mins)
                $cached = Weather::where('city', $city)
                    ->where('recorded_at', '>=', now()->subMinutes(10))
                    ->first();

                if ($cached) {
                    return [
                        'city' => $city,
                        'temp' => $cached->temp,
                        'feels_like' => $cached->feels_like,
                        'condition' => $cached->condition,
                        'humidity' => $cached->humidity,
                        'wind_speed' => $cached->wind_speed,
                        'icon' => '01d', // default icon for cached
                        'source' => 'cached'
                    ];
                }

                // Step 2: Fetch fresh data from OpenWeatherMap
                $response = Http::get('https://api.openweathermap.org/data/2.5/weather', [
                    'q' => $city,
                    'appid' => $apiKey,
                    'units' => 'metric'
                ]);

                $data = $response->json();

                if (!empty($data['main'])) {
                    // Step 3: Save or update record in DB
                    Weather::updateOrCreate(
                        ['city' => $city],
                        [
                            'temp' => $data['main']['temp'],
                            'feels_like' => $data['main']['feels_like'],
                            'condition' => $data['weather'][0]['description'],
                            'humidity' => $data['main']['humidity'],
                            'wind_speed' => $data['wind']['speed'],
                            'recorded_at' => now(),
                        ]
                    );

                    return [
                        'city' => $city,
                        'temp' => $data['main']['temp'],
                        'feels_like' => $data['main']['feels_like'],
                        'condition' => $data['weather'][0]['description'],
                        'humidity' => $data['main']['humidity'],
                        'wind_speed' => $data['wind']['speed'],
                        'icon' => $data['weather'][0]['icon'] ?? '01d',
                        'source' => 'api'
                    ];
                }

                // Step 4: Fallback if API fails
                return [
                    'city' => $city,
                    'temp' => 'N/A',
                    'feels_like' => 'N/A',
                    'condition' => 'Data not available',
                    'humidity' => 'N/A',
                    'wind_speed' => 'N/A',
                    'icon' => '01d',
                    'source' => 'none'
                ];
            });
        }

        // ✅ Collect all results after multithreading
        $results = $pool->wait(); // IMPORTANT: wait() ensures all async tasks complete

        // ✅ Return JSON response to frontend
        return response()->json($results)
            ->header('Access-Control-Allow-Origin', '*');
    }
}

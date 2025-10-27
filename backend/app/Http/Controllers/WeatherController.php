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
        // ✅ List of 10 cities
        $cities = ['Colombo', 'Tokyo', 'Seoul', 'Paris', 'New York', 'London', 'Dubai', 'Sydney', 'Moscow', 'Mumbai'];
        $apiKey = env('OPENWEATHER_API_KEY'); 

        $results = [];
        $pool = Pool::create();

        foreach ($cities as $city) {
            $pool->add(function () use ($city, $apiKey) {
                // ✅ Check if recent weather data exists (last 10 minutes)
                $cached = Weather::where('city', $city)
                    ->where('recorded_at', '>=', now()->subMinutes(10))
                    ->first();

                if ($cached) {
                    // Return cached data in full structure
                    return [
                        'city' => $city,
                        'data' => [
                            'coord' => ['lon' => 0, 'lat' => 0], // optional
                            'weather' => [
                                [
                                    'id' => 0,
                                    'main' => $cached->condition,
                                    'description' => $cached->condition,
                                    'icon' => '01d', // default icon
                                ]
                            ],
                            'base' => 'stations',
                            'main' => [
                                'temp' => $cached->temp,
                                'feels_like' => $cached->feels_like,
                                'temp_min' => $cached->temp,
                                'temp_max' => $cached->temp,
                                'pressure' => 1010,
                                'humidity' => $cached->humidity,
                                'sea_level' => 1010,
                                'grnd_level' => 1010,
                            ],
                            'visibility' => 10000,
                            'wind' => ['speed' => $cached->wind_speed, 'deg' => 0],
                            'clouds' => ['all' => 0],
                            'dt' => now()->timestamp,
                            'sys' => ['type' => 1, 'id' => 0, 'country' => ''],
                            'timezone' => 0,
                            'id' => 0,
                            'name' => $city,
                            'cod' => 200,
                        ]
                    ];
                }

                // ✅ If no recent cache, fetch live data
                $response = Http::get('https://api.openweathermap.org/data/2.5/weather', [
                    'q' => $city,
                    'appid' => $apiKey,
                    'units' => 'metric'
                ]);

                $data = $response->json();

                // ✅ Save to database if valid
                if (!empty($data['main'])) {
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
                }

                return [
                    'city' => $city,
                    'data' => $data,
                ];
            });
        }

        foreach ($pool as $output) {
            $results[] = $output;
        }

        return response()->json($results);
    }
}

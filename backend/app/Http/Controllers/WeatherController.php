<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Weather;

class WeatherController extends Controller
{
    public function getWeather()
    {
        $cities = ['Colombo', 'Tokyo', 'Seoul', 'Paris', 'New York'];
        $apiKey = env('OPENWEATHER_API_KEY');

        $results = [];

        foreach ($cities as $city) {
            // ✅ Fetch live data
            $response = Http::get('https://api.openweathermap.org/data/2.5/weather', [
                'q' => $city,
                'appid' => $apiKey,
                'units' => 'metric'
            ]);

            $data = $response->json();

            // ✅ Save or update database record
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

                // ✅ Push to results
                $results[] = [
                    'city' => $city,
                    'temp' => $data['main']['temp'],
                    'feels_like' => $data['main']['feels_like'],
                    'condition' => $data['weather'][0]['description'],
                    'humidity' => $data['main']['humidity'],
                    'wind_speed' => $data['wind']['speed'],
                    'icon' => $data['weather'][0]['icon'],
                ];
            }
        }

        return response()->json($results);
    }
}

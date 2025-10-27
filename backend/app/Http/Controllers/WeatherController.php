<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Spatie\Async\Pool;
use App\Models\Weather; // ✅ Our Weather model

class WeatherController extends Controller
{
    public function getWeather()
    {
        $cities = ['Colombo', 'Tokyo', 'Seoul', 'Paris', 'New York'];
        $apiKey = env('OPENWEATHER_API_KEY'); // ✅ Use key from .env

        $pool = Pool::create();
        foreach ($cities as $city) {
            $pool->add(function () use ($city, $apiKey) {
                $response = Http::get('https://api.openweathermap.org/data/2.5/weather', [
                    'q' => $city,
                    'appid' => $apiKey,
                    'units' => 'metric'
                ]);

                $data = $response->json();

                // ✅ Save to database
                if (!empty($data['main'])) {
                    Weather::create([
                        'city' => $city,
                        'temp' => $data['main']['temp'],
                        'feels_like' => $data['main']['feels_like'],
                        'condition' => $data['weather'][0]['description'],
                        'humidity' => $data['main']['humidity'],
                        'wind_speed' => $data['wind']['speed'],
                        'recorded_at' => now(),
                    ]);
                }

                return [
                    'city' => $city,
                    'data' => $data,
                ];
            });
        }

        $results = [];
        foreach ($pool as $output) {
            $results[] = $output;
        }

        return response()->json($results);
    }
}

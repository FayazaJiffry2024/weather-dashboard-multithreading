<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Spatie\Async\Pool;

class WeatherController extends Controller
{
    public function getWeather()
    {
        // ✅ List of cities
        $cities = ['Colombo', 'Tokyo', 'Seoul', 'Paris', 'New York'];

        // ✅ Your API key from OpenWeatherMap.org
        $apiKey = 'YOUR_API_KEY'; // 🔥 replace this

        $pool = Pool::create();

        // ✅ Add each city to the async pool (runs concurrently)
        foreach ($cities as $city) {
            $pool->add(function () use ($city, $apiKey) {
                $response = Http::get('https://api.openweathermap.org/data/2.5/weather', [
                    'q' => $city,
                    'appid' => $apiKey,
                    'units' => 'metric'
                ]);

                return [
                    'city' => $city,
                    'data' => $response->json(),
                ];
            });
        }

        // ✅ Collect all async results
        $results = [];
        foreach ($pool as $output) {
            $results[] = $output;
        }

        // ✅ Return all weather data as JSON
        return response()->json($results);
    }
}

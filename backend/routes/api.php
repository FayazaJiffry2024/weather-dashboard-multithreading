<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WeatherController;

// Return live weather for 5 cities
Route::get('/weather', [WeatherController::class, 'getWeather']);

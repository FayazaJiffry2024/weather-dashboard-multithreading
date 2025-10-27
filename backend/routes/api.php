<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WeatherController;

// Return weather from database
Route::get('/weather', [WeatherController::class, 'getWeather']);

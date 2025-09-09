<?php
use App\Http\Controllers\WeatherController;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('welcome');
});


Route::get('/api/forecast', [WeatherController::class, 'getForecast']);

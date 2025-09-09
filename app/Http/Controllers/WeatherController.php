<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\WeatherService;
use Illuminate\Http\JsonResponse;


class WeatherController extends Controller
{
    protected WeatherService $weatherService;

    /**
     * Constructor 
     */
    public function __construct(WeatherService $weatherService)
    {
        $this->weatherService = $weatherService;
        
    }

    /**
     * Get weather forecast for a specified city
     */
    public function getForecast(Request $request): JsonResponse
    {
        // optional Validate the city parameter because normalizedCity doing city validation as well 
        $request->validate([
            'city' => 'required|string|min:2|max:100'
        ]);

        $city = $request->query('city');

        try {
            // Call your WeatherService method 
            $forecast = $this->weatherService->fetchFiveDayForecast($city);
            
            

            return response()->json([
                'data' => $forecast
               
            ]);

        } catch (Exception $e) {
            Log::error('Weather forecast error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Unable to fetch weather forecast for ' . $city,
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }
}

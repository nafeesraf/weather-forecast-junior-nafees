<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use App\Helpers\CityHelper;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;


class WeatherService
{
    protected CityHelper $cityHelper;
    private $apiKey;
    private $baseUrl;
    private $cacheTime;


    /**
     * Constructor
     */
    public function __construct(CityHelper $cityHelper)
    {
        # Getting the Weather Map Keys and URL and Helper Instance
        $this->cityHelper = $cityHelper;
        $this->apiKey = env('APP_WEATHER_KEY');
        $this->baseUrl = env('APP_WEATHER_URL');
        $this->cacheTime = env('APP_CACHE_TIME');
    }

    public function fetchFiveDayForecast(string $city): array
    {


        $normalizedCity = $this->cityHelper->normalizeCityName($city);
        if (!$normalizedCity) {
            return ['city' => $city, 'days' => [], 'error' => 'Invalid city'];
        }

        try {


            $cacheforecast = Cache::remember($normalizedCity, Carbon::now()->addSeconds((int)$this->cacheTime), function () use ($normalizedCity) {

                $url = $this->baseUrl . '?city=' . urlencode($normalizedCity) . ',AU&days=5&key=' . urlencode($this->apiKey);

                $response = Http::get($url)->throw();
                if (!$response->ok()) {
                    return ['city' => $city, 'days' => [], 'error' => 'API error'];
                }


                $json = $response->json();


                $days = [];
                if (isset($json['data']) && is_array($json['data'])) {
                    foreach ($json['data'] as $day) {
                        $max = isset($day['max_temp']) ? (float)$day['max_temp'] : null;
                        $min = isset($day['min_temp']) ? (float)$day['min_temp'] : null;

                        // Simplified average calculation with clear priority
                        if ($max !== null && $min !== null) {
                            $avg = ($max + $min) / 2;
                        } elseif (isset($day['temp'])) {
                            $avg = (float)$day['temp'];
                        } elseif (isset($day['app_max_temp'], $day['app_min_temp'])) {
                            $avg = ((float)$day['app_max_temp'] + (float)$day['app_min_temp']) / 2;
                        } else {
                            $avg = 0.0;
                        }

                        $days[] = [
                            'forcast_date' => $day['valid_date'] ?? 'unknown',
                            'forcast_avg' => is_null($avg) ? null : round($avg),
                            'forcast_max' => is_null($max) ? null : round($max),
                            'forcast_min' => is_null($min) ? null : round($min),
                        ];
                    }
                }

               
                return [
                    'success' => true,
                    'city' => $normalizedCity,
                    'days' => $days
                ];

            }); # end of cache

            return $cacheforecast;


        } catch (\Exception $e) {
            Log::error('Weather forecast error: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Unable to fetch weather forecast for ' . $city,
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ];
        }


    }


}

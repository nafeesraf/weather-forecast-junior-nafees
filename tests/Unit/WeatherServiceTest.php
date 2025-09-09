<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\WeatherService;
use App\Helpers\CityHelper;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Carbon;
use Mockery;
use Exception;

class WeatherServiceTest extends TestCase
{
    protected $cityHelperMock;
    protected $weatherService;

    protected function setUp(): void
    {
        parent::setUp();

        // Mocking the CityHelper dependency
        $this->cityHelperMock = Mockery::mock(CityHelper::class);

        // Setting environment variables
        Config::set('app.debug', true);
        putenv('APP_WEATHER_KEY=test_key');
        putenv('APP_WEATHER_URL=https://api.weather.test');
        putenv('APP_CACHE_TIME=600');

        $this->weatherService = new WeatherService($this->cityHelperMock);
    }

    /** @test
     * Test when city name is invalid and normalization fails
     */
    public function it_returns_error_for_invalid_city()
    {
        $this->cityHelperMock->shouldReceive('normalizeCityName')
            ->once()
            ->with('InvalidCity')
            ->andReturn(null);

        $result = $this->weatherService->fetchFiveDayForecast('InvalidCity');

        $this->assertEquals([
            'city' => 'InvalidCity',
            'days' => [],
            'error' => 'Invalid city'
        ], $result);
    }

    /** @test
     * Test successful API response and forecast parsing
     */
    public function it_returns_forecast_data_for_valid_city()
    {
        $normalizedCity = 'Brisbane';

        $this->cityHelperMock->shouldReceive('normalizeCityName')
            ->once()
            ->with('Brisbane')
            ->andReturn($normalizedCity);

        Cache::shouldReceive('remember')
            ->once()
            ->andReturn([
                'success' => true,
                'city' => $normalizedCity,
                'days' => [
                    [
                        'forcast_date' => '2025-09-09',
                        'forcast_avg' => 25,
                        'forcast_max' => 30,
                        'forcast_min' => 20,
                    ]
                ]
            ]);

        $result = $this->weatherService->fetchFiveDayForecast('Brisbane');

        $this->assertTrue($result['success']);
        $this->assertEquals('Brisbane', $result['city']);
        $this->assertCount(1, $result['days']);
    }

    /** @test
     * Test API failure scenario (e.g., HTTP error)
     */
    public function it_handles_api_failure_gracefully()
    {
        $normalizedCity = 'Sydney';

        $this->cityHelperMock->shouldReceive('normalizeCityName')
            ->once()
            ->with('Sydney')
            ->andReturn($normalizedCity);

        Cache::shouldReceive('remember')
            ->once()
            ->andThrow(new \Exception('API unreachable'));

        Log::shouldReceive('error')->once();

        $result = $this->weatherService->fetchFiveDayForecast('Sydney');

        $this->assertFalse($result['success']);
        $this->assertEquals('Unable to fetch weather forecast for Sydney', $result['message']);
        $this->assertEquals('API unreachable', $result['error']);
    }


    /** @test
     * Test fallback average temperature logic when max/min are missing
     */
    public function it_calculates_average_from_temp_if_max_min_missing()
    {
        $normalizedCity = 'Melbourne';

        $this->cityHelperMock->shouldReceive('normalizeCityName')
            ->once()
            ->with('Melbourne')
            ->andReturn($normalizedCity);

        $mockResponse = [
            'data' => [
                [
                    'valid_date' => '2025-09-09',
                    'temp' => 22.5
                ]
            ]
        ];

        Http::fake([
            '*' => Http::response($mockResponse, 200)
        ]);

        Cache::shouldReceive('remember')
            ->once()
            ->andReturnUsing(function () use ($normalizedCity, $mockResponse) {
                return [
                    'success' => true,
                    'city' => $normalizedCity,
                    'days' => [
                        [
                            'forcast_date' => '2025-09-09',
                            'forcast_avg' => 23,
                            'forcast_max' => null,
                            'forcast_min' => null,
                        ]
                    ]
                ];
            });

        $result = $this->weatherService->fetchFiveDayForecast('Melbourne');

        $this->assertEquals(23, $result['days'][0]['forcast_avg']);
    }
}

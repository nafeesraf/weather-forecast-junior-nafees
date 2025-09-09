<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\WeatherService;
use Illuminate\Support\Facades\Log;
// ...existing code...

class ForecastCommand extends Command
{
    protected $signature = 'forecast {cities?*}';
    protected $description = 'Show tabulated data of 5 day forecast for given cities (prompts if none provided)';
    protected WeatherService $weatherService;

    /**
     * Constructor
     */
    public function __construct(WeatherService $weatherService)
    {
        parent::__construct();
        $this->weatherService = $weatherService;

    }

    public function handle()
    {
        try {

            //throw new \Exception("Cannot divide by zero!");

        $cities = $this->argument('cities');

        if (empty($cities)) {
            $this->info('No cities provided. Please enter a comma-separated list (e.g., Brisbane, Gold Coast, Sunshine Coast).');
            $input = $this->ask('Cities');
            $cities = array_map('trim', explode(',', (string)$input));
        }


        $header = ['City', 'Day 1', 'Day 2', 'Day 3', 'Day 4', 'Day 5'];
        $this->line(implode("\t", $header));

        foreach ($cities as $city) {
            if ($city === '')
                continue;
            $data = $this->weatherService->fetchFiveDayForecast($city);

            if (isset($data['error'])) {
                $this->line($city . "\t" . $data['error']);
                continue;
            }

            $cells = [$data['city']];
            $i = 0;
            foreach ($data['days'] as $day) {
                $part = 'Avg: ' . (isset($day['forcast_avg']) ? $day['forcast_avg'] : 'NA')
                    . ', Max: ' . (isset($day['forcast_max']) ? $day['forcast_max'] : 'NA')
                    . ', Low: ' . (isset($day['forcast_min']) ? $day['forcast_min'] : 'NA');
                $cells[] = $part;
                $i++;
                if ($i >= 5)
                    break;
            }
            while (count($cells) < 6) {
                $cells[] = 'N/A';
            }

            $this->line(implode("\t", $cells));
        }

        return 0;

        } catch (\Exception $e) { // Catch any other general exception
            $this->error('An unexpected error occurred: ' . $e->getMessage());
            // Log the general error
            \Log::error('General error in MyCustomCommand: ' . $e->getMessage());
        }
    }
}

<?php

 namespace App\Helpers;

class CityHelper
{
    /**
     * Mapping of city aliases to their canonical names
     * @var array<string, string>
     */
    private const CITY_ALIASES = [
        'brisbane'        => 'Brisbane',
        'bris'            => 'Brisbane', 
        'brissy'          => 'Brisbane',
        'gold coast'      => 'Gold Coast',
        'goldcoast'       => 'Gold Coast',
        'gc'              => 'Gold Coast',
        'sunshine coast'  => 'Sunshine Coast',
        'sunshinecoast'   => 'Sunshine Coast',
        'sc'              => 'Sunshine Coast',
    ];

     /**
     * Constructor 
     */
    public function __construct()
    {
        //
    }
     /**
     * Normalizes a city name to its canonical form
     *
     * @param string $city The input city name to normalize
     * @return string|null The canonical city name, or null if not found
     */

    public function normalizeCityName(string $city): ?string
    {
        $normalizedInput = trim(strtolower($city));
        
        // Early return for empty input
        if (empty($normalizedInput)) {
            return null;
        }
        
        // Direct array lookup with null coalescing for efficiency
        return self::CITY_ALIASES[$normalizedInput] ?? null;
    }
}

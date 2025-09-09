<?php

namespace Tests\Unit;

use App\Helpers\CityHelper;
use PHPUnit\Framework\TestCase;

class CityHelperTest extends TestCase
{

    private CityHelper $helper;

    protected function setUp(): void
    {
        parent::setUp();
        $this->helper = new CityHelper();
    }

    /**
     * Test normalization of various aliases for Brisbane.
     * Ensures casing and spacing variations still resolve to "Brisbane".
     */
    public function testNormalizeValidBrisbaneAliases(): void
    {
        $aliases = ['brisbane', 'Brisbane', 'BRISBANE', 'bris', 'Bris', 'brissy', ' Brissy '];
        foreach ($aliases as $alias) {
            $this->assertSame('Brisbane', $this->helper->normalizeCityName($alias));
        }
    }

    /**
     * Test normalization of various aliases for Gold Coast.
     * Confirms that common abbreviations and spacing are handled correctly.
     */
    public function testNormalizeValidGoldCoastAliases(): void
    {
        $aliases = ['gold coast', 'Gold Coast', 'GOLDCOAST', 'goldcoast', 'gc', ' GC '];
        foreach ($aliases as $alias) {
            $this->assertSame('Gold Coast', $this->helper->normalizeCityName($alias));
        }
    }

    /**
     * Test normalization of various aliases for Sunshine Coast.
     * Verifies that both full names and abbreviations resolve properly.
     */
    public function testNormalizeValidSunshineCoastAliases(): void
    {
        $aliases = ['sunshine coast', 'Sunshine Coast', 'sunshinecoast', 'SC', ' sc '];
        foreach ($aliases as $alias) {
            $this->assertSame('Sunshine Coast', $this->helper->normalizeCityName($alias));
        }
    }

    /**
     * Test behavior when an unknown or unsupported city is provided.
     * Should return null for cities not in the alias map.
     */
    public function testNormalizeUnknownCityReturnsNull(): void
    {
        $this->assertNull($this->helper->normalizeCityName('melbourne'));
        $this->assertNull($this->helper->normalizeCityName('sydney'));
        $this->assertNull($this->helper->normalizeCityName(''));
        $this->assertNull($this->helper->normalizeCityName('   '));
    }

    /**
     * Test that normalization is case-insensitive and trims whitespace.
     * Ensures input is sanitized before lookup.
     */
    public function testNormalizeCityIsCaseInsensitiveAndTrimmed(): void
    {
        $this->assertSame('Brisbane', $this->helper->normalizeCityName('  BRIS  '));
        $this->assertSame('Gold Coast', $this->helper->normalizeCityName("\ngc\n"));
        $this->assertSame('Sunshine Coast', $this->helper->normalizeCityName("\tsc\t"));
    }



}

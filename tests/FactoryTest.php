<?php

namespace tests;

use FuzzyJsonSearch\SearchFactory;

/**
 * Class Factory test
 * @package tests
 * @author robotomize@gmail.com
 */
class FactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var array
     */
    private static $testUrlName = ['cities.json', 'airlines.json', 'biographical-directory-footnotes.json'];

    /**
     * @var array
     */
    private static $testMatchString = [
        'vladvostk',            // 0
        'vladivostok',          // 1
        'moscow',               // 2
        'mcow',                 // 3
        'ekateburg',            // 4
        'mscow',                // 5
        'yuzhno-sakhalinsk',    // 6
        'yugnosakhalinsk',      // 7
        'yugno-sahalinsk',      // 8
        'newyork',              // 9
        'korsakov',             // 10
        'sidney',               // 11
        'berdlen',              // 12
        'koleningrat',          // 13
        'smalyansk',            // 14
        'another city',         // 15
    ];

    /**
     * @var array
     */
    private static $testMatchStringAnother = ['aeroflot', 'kaskoflot', 's7', 'sseven'];

    /**
     * @var array
     */
    private static $bioForTest =['Christensen', 'Maxwell'];

    /**
     * @var string
     */
    private static $prefix = __DIR__ . '/../src/data/';

    public function testCities()
    {
        //print SearchFactory::createSearchEngine(self::$prefix . self::$testUrlName[2], self::$bioForTest[0], 1, true, false);
    }

    public function testBio()
    {}

}


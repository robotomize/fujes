<?php

namespace tests;

use FuzzyJsonSearch\SearchFacade;

/**
 * Class FacadeTest
 * @package tests
 * @author robotomize@gmail.com
 */
class FacadeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var array
     */
    private static $testUrlName = ['cities.json', 'airlines.json'];

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
        'another city'          // 15
    ];

    /**
     * @var array
     */
    private static $testMatchStringAnother = ['aeroflot', 'kaskoflot', 's7', 'sseven'];

    /**
     * @var string
     */
    private static $prefix = __DIR__ . '/../src/data/';

    public function testFetchOne()
    {
        $tt = new SearchFacade(self::$prefix . 'cities.json', self::$testMatchString[4], 1, false, false);
        $this->assertEquals('Ekaterinburg', $tt->fetchOne()['name']);
        $tt->setMatchString(self::$testMatchString[2]);
        $this->assertEquals('Moscow', $tt->fetchOne()['name']);
        $tt->setMatchString(self::$testMatchString[0]);
        $this->assertEquals('Vladivostok', $tt->fetchOne()['name']);
        $tt->setMatchString(self::$testMatchString[1]);
        $this->assertEquals('Vladivostok', $tt->fetchOne()['name']);
        $tt->setMatchString(self::$testMatchString[6]);
        $this->assertEquals('Yuzhno-Sakhalinsk', $tt->fetchOne()['name']);
        $tt->setMatchString(self::$testMatchString[7]);
        $this->assertEquals('Yuzhno-Sakhalinsk', $tt->fetchOne()['name']);
        $tt->setMatchString(self::$testMatchString[8]);
        $this->assertEquals('Yuzhno-Sakhalinsk', $tt->fetchOne()['name']);

    }

    public function testFetchFew()
    {

    }

    public function testFetchAll()
    {

    }
}


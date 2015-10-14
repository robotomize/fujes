<?php

namespace tests;

require __DIR__ . '/../src/autoload.php';

use FuzzyJsonSearch\SearchEngine;
use FuzzyJsonSearch\SearchTreeWalk;

/**
 * Class SearchEngineTest
 * @package tests
 * @author robotomize@gmail.com
 */
class SearchEngineTest extends \PHPUnit_Framework_TestCase
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

    /**
     * @param $string
     *
     * @return bool
     */
    private function isJsonTest($string) {
        json_decode($string);
        if (json_last_error() == JSON_ERROR_NONE) {
            if (substr($string, 0, 1) === '[' && substr($string, -1) === ']') {
                return TRUE;
            }
            elseif (substr($string, 0, 1) === '{' && substr($string, -1) === '}') {
                return TRUE;
            }
            else {
                return FALSE;
            }
        }
    }

    /**
     * Direct compare test
     */
    public function testCompareTwoValueDirect()
    {
        $searchEngine = new SearchEngine(
            self::$prefix . self::$testUrlName[0],
            self::$testMatchString[8],
            1,
            false,
            false
        );
        $searchEngine->run();

        $searchWalk = new SearchTreeWalk($searchEngine->getJsonTree(), self::$testMatchString[8], false);
        $this->assertEquals(
            true,
            $searchWalk->directCompareTwoString('yugno-sahalinsk', mt_rand(0, 10))
        );

        $searchWalk->setMatchString(self::$testMatchString[11]);
        $this->assertEquals(
            true,
            $searchWalk->directCompareTwoString('sidney', mt_rand(0, 10))
        );

        $searchWalk->setMatchString(self::$testMatchString[13]);
        $this->assertEquals(
            true,
            $searchWalk->directCompareTwoString('koleningrat', mt_rand(0, 10))
        );
    }

    /**
     * Fuzzy compare test
     */
    public function testCompareTwoValueFuzzy()
    {
        $searchEngine = new SearchEngine(
            self::$prefix . self::$testUrlName[0],
            self::$testMatchString[15],
            1,
            false,
            false
        );
        $searchEngine->run();

        $searchWalk = new SearchTreeWalk($searchEngine->getJsonTree(), self::$testMatchString[8], false);

        $searchWalk->compareStart('yugno-sahalinsk', mt_rand(0, 10));
        $this->assertEquals(1, count($searchWalk->getDirectMatch()));

        $searchWalk->compareStart('yugnosakhalinsk', mt_rand(0, 10));
        $this->assertEquals(2, count($searchWalk->getDirectMatch()));

        $searchWalk->compareStart('yuzhno-sakhalinsk', mt_rand(0, 10));
        $this->assertEquals(3, count($searchWalk->getDirectMatch()));
    }

    /**
     * Parsing json test
     */
    public function testParseJson()
    {
        $searchEngine = new SearchEngine(self::$prefix . self::$testUrlName[0], self::$testMatchString[0], 1, false, false);
        $searchEngine->run();

        $this->assertEquals(9369, count($searchEngine->getJsonTree()));

        $searchEngine = new SearchEngine(self::$prefix . self::$testUrlName[0], self::$testMatchString[0], 1, true, false);
        $searchEngine->run();

        $this->assertTrue($this->isJsonTest($searchEngine->getJsonData()));

        $searchEngine = new SearchEngine(self::$prefix . self::$testUrlName[0], self::$testMatchString[1], 1, false, false);
        $searchEngine->run();

        $this->assertEquals('Vladivostok', $searchEngine->fetchOne()['name']);

        $searchEngine = new SearchEngine(self::$prefix . self::$testUrlName[0], self::$testMatchString[2], 1, false, false);
        $searchEngine->run();

        $this->assertEquals('Moscow', $searchEngine->fetchOne()['name']);

        $searchEngine = new SearchEngine(self::$prefix . self::$testUrlName[0], self::$testMatchString[3], 1, false, false);
        $searchEngine->run();

        $this->markTestSkipped('Moscow', $searchEngine->fetchOne()['name']);

        $searchEngine = new SearchEngine(self::$prefix . self::$testUrlName[0], self::$testMatchString[2], 1, false, true);
        $searchEngine->run();

        $this->assertEquals(3, count($searchEngine->fetchFew(3)['name']));
        $this->assertEquals(1, count($searchEngine->fetchFew(1)['name']));

        $searchEngine = new SearchEngine(self::$prefix . self::$testUrlName[0], self::$testMatchString[4], 1, false, true);
        $searchEngine->run();

        $this->assertEquals('Ekaterinburg', count($searchEngine->fetchOne()['name']));
    }

    /**
     * Pre search test
     */
    public function testPreSearch()
    {
        $searchEngine = new SearchEngine(self::$prefix . self::$testUrlName[0], self::$testMatchString[10], 1, false, false);
        $searchEngine->run();
        $this->assertEquals('Komako', $searchEngine->fetchOne()['name']);
    }
}

<?php
/**
 * This file is part of the FuJaySearch package.
 * @link    https://github.com/robotomize/FuJaySearch
 * @license http://www.opensource.org/licenses/mit-license.php MIT (see the LICENSE file)
 */

namespace tests;

use robotomize\Fujes\SearchFactory;

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
        'mocow',                // 3
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
    private static $prefix = __DIR__ . '/../src/robotomize/data/';

    /**
     * @group factory
     */
    public function testCities()
    {
        $this->assertEquals('Vladivostok', SearchFactory::createSearchEngine(
            self::$prefix . self::$testUrlName[0],
            self::$testMatchString[0],
            2,
            false,
            false,
            1,
            'dev'
        )->fetchOne()['name']);
        $this->assertEquals('Vladivostok', SearchFactory::createSearchEngine(
            self::$prefix . self::$testUrlName[0],
            self::$testMatchString[1],
            1,
            false,
            false,
            1,
            'dev'
        )->fetchOne()['name']);
        $this->assertEquals('Moscow', SearchFactory::createSearchEngine(
            self::$prefix . self::$testUrlName[0],
            self::$testMatchString[2],
            1,
            false,
            false,
            1,
            'dev'
        )->fetchOne()['name']);
        $this->assertEquals('Moscow', SearchFactory::createSearchEngine(
            self::$prefix . self::$testUrlName[0],
            self::$testMatchString[3],
            1,
            false,
            false,
            1,
            'dev'
        )->fetchOne()['name']);
        $this->assertEquals('Ekaterinburg', SearchFactory::createSearchEngine(
            self::$prefix . self::$testUrlName[0],
            self::$testMatchString[4],
            2,
            false,
            false,
            1,
            'dev'
        )->fetchOne()['name']);
        $this->assertEquals('Moscow', SearchFactory::createSearchEngine(
            self::$prefix . self::$testUrlName[0],
            self::$testMatchString[5],
            1,
            false,
            false,
            1,
            'dev'
        )->fetchOne()['name']);
        $this->assertEquals('Yuzhno-Sakhalinsk', SearchFactory::createSearchEngine(
            self::$prefix . self::$testUrlName[0],
            self::$testMatchString[6],
            1,
            false,
            false,
            1,
            'dev'
        )->fetchOne()['name']);
        $this->assertEquals('Yuzhno-Sakhalinsk', SearchFactory::createSearchEngine(
            self::$prefix . self::$testUrlName[0],
            self::$testMatchString[7],
            2,
            false,
            false,
            1,
            'dev'
        )->fetchOne()['name']);
        $this->assertEquals('Yuzhno-Sakhalinsk', SearchFactory::createSearchEngine(
            self::$prefix . self::$testUrlName[0],
            self::$testMatchString[8],
            2,
            false,
            false,
            1,
            'dev'
        )->fetchOne()['name']);
        $this->assertEquals('Newport', SearchFactory::createSearchEngine(
            self::$prefix . self::$testUrlName[0],
            self::$testMatchString[9],
            2,
            false,
            false,
            1,
            'dev'
        )->fetchOne()['name']);
        $this->assertEquals('Kaliningrad', SearchFactory::createSearchEngine(
            self::$prefix . self::$testUrlName[0],
            self::$testMatchString[13],
            2,
            false,
            false,
            1,
            'dev'
        )->fetchOne()['name']);
        $this->assertEquals('Smolensk', SearchFactory::createSearchEngine(
            self::$prefix . self::$testUrlName[0],
            self::$testMatchString[14],
            2,
            false,
            false,
            1,
            'dev'
        )->fetchOne()['name']);


    }

    /**
     * @group Bio
     */
    public function testBio()
    {
        $this->assertEquals('56939', SearchFactory::createSearchEngine(
            self::$prefix . self::$testUrlName[2],
            self::$bioForTest[0],
            1,
            false,
            false,
            1,
            'dev'
        )->fetchOne()['line']);
        $this->assertEquals('57113', SearchFactory::createSearchEngine(
            self::$prefix . self::$testUrlName[2],
            self::$bioForTest[1],
            1,
            false,
            false,
            1,
            'dev'
        )->fetchOne()['line']);
    }
}

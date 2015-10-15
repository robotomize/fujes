<?php
/**
 * This file is part of the FuJaySearch package.
 * @link    https://github.com/robotomize/FuJaySearch
 * @license http://www.opensource.org/licenses/mit-license.php MIT (see the LICENSE file)
 */

namespace tests;

use FuzzyJsonSearch\SearchFacade;

/**
 * Class Facade Test
 * @package tests
 * @author robotomize@gmail.com
 */
class FacadeTest extends \PHPUnit_Framework_TestCase
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

    /**
     * @group fetch1
     */
    public function testFetchOne()
    {
        $tt = new SearchFacade(self::$prefix . self::$testUrlName[0], self::$testMatchString[4], 2, false, false, 'master');
        $this->assertEquals('Ekaterinburg', $tt->fetchOne()['name']);

        $tt->setDepth(1);
        $tt->setMatchString(self::$testMatchString[2]);

        $this->assertEquals('Moscow', $tt->fetchOne()['name']);

        $tt->setMatchString(self::$testMatchString[0]);
        $tt->setDepth(2);
        $this->assertEquals('Vladivostok', $tt->fetchOne()['name']);

        $tt->setMatchString(self::$testMatchString[1]);
        $tt->setDepth(1);
        $this->assertEquals('Vladivostok', $tt->fetchOne()['name']);

        $tt->setMatchString(self::$testMatchString[6]);
        $this->assertEquals('Yuzhno-Sakhalinsk', $tt->fetchOne()['name']);

        $tt->setMatchString(self::$testMatchString[7]);
        $tt->setDepth(2);
        $this->assertEquals('Yuzhno-Sakhalinsk', $tt->fetchOne()['name']);

        $tt->setMatchString(self::$testMatchString[8]);
        $this->assertEquals('Yuzhno-Sakhalinsk', $tt->fetchOne()['name']);

    }

    /**
     * @group fetch2
     */
    public function testFetchFew()
    {
        $tt = new SearchFacade(self::$prefix . self::$testUrlName[0], self::$testMatchString[4], 1, false, true);
        $this->assertEquals(3, count($tt->fetchFew(3)));
    }

    /**
     * @group fetch3
     */
    public function testFetchAll()
    {
        $tt = new SearchFacade(self::$prefix . self::$testUrlName[0], self::$testMatchString[4], 1, false, true);
        $this->markTestSkipped(9369, count($tt->fetchAll()));
    }
}

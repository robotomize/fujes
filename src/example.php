<?php
/**
 * This file is part of the fujes package.
 * @link    https://github.com/robotomize/FuJaySearch
 * @license http://www.opensource.org/licenses/mit-license.php MIT (see the LICENSE file)
 */

/**
 * Some examples of using the search.
 */
ini_set('memory_limit', '1024M');

use robotomize\Fujes\SearchFacade;
use robotomize\Fujes\SearchFactory;
use robotomize\Utils\ExceptionWrap;

require __DIR__ . '/autoload.php';
require __DIR__ . '/../vendor/autoload.php';

/**
 * Helper options. Search into biographical-directory-footnotes.json.
 * Match string Christensen
 * Output encode to json
 */
$options = [
    'json_file_name' => __DIR__ . '/robotomize/data/biographical-directory-footnotes.json', // json file
    'search_string' => 'Christensen',  // match string
    'depth_into_array' => '1',      // depth into output
    'output_json' => true,     // encode to json or output php array
    'multiple_result' => true, // multiple result or find one value?
    'search_quality' => 1,    // 1 best quality search
    'version' => 'dev'  // dev or master, logging exceptions && code event
];

$searchObject = new SearchFacade(
    $options['json_file_name'],
    $options['search_string'],
    $options['depth_into_array'],
    $options['output_json'],
    $options['multiple_result'],
    $options['search_quality'],
    $options['version']
);
\PHP_Timer::start();
print $searchObject->fetchOne() . PHP_EOL;
//print_r($searchObject->get)

//die();
/**
 * Output this
 *
 * {"item":"Donna Christian-Green, St. Croix ","note":"[5125:
 * Biographical information under Donna Marie Christian Christensen. ]","line":56939}
 *
 */

/**
 * Get exception
 */
try {
    print $searchObject->fetchFew(3) . PHP_EOL;
} catch (\Exception $ex) {
    print $ex->getMessage() . PHP_EOL; // -> multipleResult flag off, use $this->setMultipleResult(true)
                            // and call this function again
}

$searchObject->setMultipleResult(true);

/**
 * And this work
 */
print $searchObject->fetchFew(3) . PHP_EOL;

/**
 *
 * multipleResult flag off, use $this->setMultipleResult(true) and call this function again
 * {"item":"Donna MC Christensen, St. Croix ","note":"[5141:  Biographical information under Donna Marie Christian
 * Christensen. ]","line":57672}{"item":"Donna MC Christensen, St. Croix ","note":"[5141:
 * Biographical information under Donna Marie Christian Christensen. ]","line":57672}{"item":"Donna Christian-Green,
 * St. Croix ","note":"[5125:  Biographical information under Donna Marie Christian Christensen. ]","line":56939}%
 */

/**
 * The following example, you can use the factory.
 */
print SearchFactory::find(
    __DIR__ . '/../src/robotomize/data/cities.json',
    'vladvostk',
    2,
    false,
    true,
    1,
    'dev'
)->fetchOne() . PHP_EOL;    // print Vladivostok

print SearchFactory::find(
    __DIR__ . '/../src/robotomize/data/cities.json',
    'Mosco',
    1,
    true,
    true,
    1,
    'dev'
)->fetchOne() . PHP_EOL;    // print

print SearchFactory::find(
    __DIR__ . '/../src/robotomize/data/biographical-directory-footnotes.json',
    'Lincoln',
    1,
    true,
    true,
    1,
    'dev'
)->fetchFew(10) . PHP_EOL;    // print

$exp = new ExceptionWrap('dev');
try {
    /**
     * I want to find some planes.
     */
    print SearchFactory::find('http://api.travelpayouts.com/data/planes.json ', 'Tu')->fetchOne() . PHP_EOL;
    print SearchFactory::find('http://api.travelpayouts.com/data/planes.json ', 'Boing 7')->fetchOne() . PHP_EOL;
    print SearchFactory::find('http://api.travelpayouts.com/data/planes.json ', 'An24')->fetchOne() . PHP_EOL;

    /**
     * I want to find some airports =)
     */
    print SearchFactory::find('http://api.travelpayouts.com/data/airports.json ', 'Sheremetievo')->fetchOne() . PHP_EOL;
    print SearchFactory::find('http://api.travelpayouts.com/data/airports.json ', 'Domogedov')->fetchOne() . PHP_EOL;
    print SearchFactory::find('http://api.travelpayouts.com/data/airports.json ', 'Yugnosahalinsk')->fetchOne().PHP_EOL;
    print SearchFactory::find('http://api.travelpayouts.com/data/airports.json ', 'Puklovo')->fetchOne() . PHP_EOL;
} catch (\Exception $ex) {
    $exp->push($ex);
    try {
        $exp->saveToDisk($ex);
    } catch (\Exception $e) {
        print sprintf('Exception in %s, in %s line with message %s', $e->getFile(), $e->getLine(), $e->getMessage());
    }
}
/**
 * {"code":"MOW","name":"Moscow",
 * "coordinates":{"lon":37.617633,"lat":55.755786},"time_zone":"Europe\/Moscow","name_translations":
 * {"de":"Moskau","en":"Moscow","zh-CN":"\u83ab\u65af\u79d1","tr":"Moscow",
 * "ru":"\u041c\u043e\u0441\u043a\u0432\u0430","it":"Mosca","es":"Mosc\u00fa","fr":"Moscou",
 * "th":"\u0e21\u0e2d\u0e2a\u0e42\u0e01"},"country_code":"RU"}
 */
$time = \PHP_Timer::stop();
print \PHP_Timer::secondsToTimeString($time);

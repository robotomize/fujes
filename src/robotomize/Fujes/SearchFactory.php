<?php
/**
 * This file is part of the FuJaySearch package.
 * @link    https://github.com/robotomize/FuJaySearch
 * @license http://www.opensource.org/licenses/mit-license.php MIT (see the LICENSE file)
 */

namespace robotomize\Fujes;

/**
 * Class SearchEngineFactory
 * This factory for faster access to the functions of the library.
 *
 * @package FuJaySearch
 * @author  robotomize@gmail.com
 * @version
 * @usage
 * $resultArray = SearchEngineFactory::createSearchEngine('http://uWtfAPI.json', 'Avengers', 1, true)->fetchOne();
 * -> json string
 * $resultArray = SearchEngineFactory::createSearchEngine('http://uWtfAPI.json', 'Avengers', 1, false)->fetchOne();
 * -> PHP assoc array
 */
class SearchFactory
{
    /**
     *
     * @param $urlName
     * @param $matchString
     * @param $depth
     *
     * @return SearchEngine
     */
    public static function createSearchEngine($urlName, $matchString, $depth, $jsonEncode, $multipleResult)
    {
        $objectFactory = new SearchEngine($urlName, $matchString, $depth, $jsonEncode, $multipleResult);
        $objectFactory->run();
        return $objectFactory;
    }
}

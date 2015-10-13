<?php

namespace FuzzyJsonSearch;

/**
 *
 * Class SearchEngineFactory
 * This factory for faster access to the functions of the library.
 * @package jsonSearch
 * @author robotomize@gmail.com
 * @version
 * @usage
 * $resultArray = SearchEngineFactory::createSearchEngine('http://uWtfAPI.json', 'Avengers', 1, true)->fetchOne(); -> json string
 * $resultArray = SearchEngineFactory::createSearchEngine('http://uWtfAPI.json', 'Avengers', 1, false)->fetchOne(); -> PHP assoc array
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
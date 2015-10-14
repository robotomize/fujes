<?php

ini_set('memory_limit', '1024M');

use FuzzyJsonSearch\SearchFacade;
use FuzzyJsonSearch\SearchFactory;
use FuzzyJsonSearch\SearchEngine;

require __DIR__ . '/autoload.php';
require __DIR__ . '/../vendor/autoload.php';



//$tt = new SearchFacade(__DIR__ . '/data/biographical-directory-footnotes.json', 'Christensen', 1, false, false);
$tt = new SearchEngine(__DIR__ . '/data/biographical-directory-footnotes.json', 'Max', 1, false, true, 1, 'dev');

//print $tt->getVersionType();
\PHP_Timer::start();
//print_r($tt->fetchOne());
$tt->run();
//print_r($tt->getSortedScoreMatrix());
print_r($tt->fetchOne());
$time = \PHP_Timer::stop();
print \PHP_Timer::secondsToTimeString($time);
//print PHP_EOL . $tt->getMultipleResult();

//print $tt->fetchFew(4) . PHP_EOL;
//
//$vv = new SearchFacade('data/cities.json', 'vldostok', 1, false);
//print_r($vv->fetchFew(3));
//print SearchFactory::createSearchEngine('data/cities.json', 'moscow', 1, true)->fetchOne() . PHP_EOL;
//
//print SearchFactory::createSearchEngine('data/cities.json', 'moscow', 1, true)->fetchFew(2) . PHP_EOL;
//
//print_r(SearchFactory::createSearchEngine('data/cities.json', 'moscow', 1, false)->fetchFew(3));
//print_r(SearchFactory::createSearchEngine('data/cities.json', 'moscow', 1, true)->fetchAll());

<?php

ini_set('memory_limit', '1024M');

use FuzzyJsonSearch\SearchFacade;
use FuzzyJsonSearch\SearchFactory;

include 'autoload.php';
include '../vendor/autoload.php';



$tt = new SearchFacade('data/cities.json', 'vldivastok', 1, true, false);
\PHP_Timer::start();
print $tt->fetchOne() . PHP_EOL;
$time = \PHP_Timer::stop();
print \PHP_Timer::secondsToTimeString($time);
print PHP_EOL . $tt->getMultipleResult();

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

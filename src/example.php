<?php

namespace jsonSearch;

include 'SearchEngine.php';

use jsonSearch\SearchEngine;

$tt = new SearchEngine('http://api.travelpayouts.com/data/cities.json', 'Yuzno-sakhalinsk');
$tt->run();
print PHP_EOL;
print_r($tt->fetchFew(3));

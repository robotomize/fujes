# fujes - PHP Fuzzy JSON search
[![Latest Stable Version](https://poser.pugx.org/robotomize/fujes/v/stable)](https://packagist.org/packages/robotomize/fujes)
[![Code Climate2](https://scrutinizer-ci.com/g/robotomize/fujes/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/robotomize/fujes/?branch=master)
[![Code Climate](https://codeclimate.com/github/robotomize/fujes/badges/gpa.svg)](https://codeclimate.com/github/robotomize/fujes)
[![Build Status](https://travis-ci.org/robotomize/fujes.svg)](https://travis-ci.org/robotomize/fujes/)
[![Total Downloads](https://poser.pugx.org/robotomize/fujes/downloads)](https://packagist.org/packages/robotomize/fujes)
[![License](https://poser.pugx.org/robotomize/fujes/license)](https://packagist.org/packages/robotomize/fujes)

## Why?
Firstly, it is the implementation of the search on the format of the data in PHP. 
You can look up information on the fly. You can look for anything to JSON files. 
This is useful when your service accesses a different API.

The basis of the algorithm is taken Levenshtein.

## Requirements
* php 5.6+

## Installation
```sh
install composer (https://getcomposer.org/download/)
composer require robotomize/fujes
```
#### or
```sh
git clone https://github.com/robotomize/fujes.git
```
## Usage
#### Fast, minimal params, go
```php
<?php
use robotomize\Fujes\SearchFactory;

/**
* 
* `I want to find some planes.`
*/
print SearchFactory::find(
    'http://api.travelpayouts.com/data/planes.json', 
    'Tu'
)->fetchOne() . PHP_EOL;
print SearchFactory::find(
    'http://api.travelpayouts.com/data/planes.json', 
    'Boing 7'
)->fetchOne() . PHP_EOL;
print SearchFactory::find(
    'http://api.travelpayouts.com/data/planes.json', 
    'An24'
)->fetchOne() . PHP_EOL;
```
[![Pic1](https://cloud.githubusercontent.com/assets/1207984/10567867/aec0b714-7649-11e5-8435-cc87dc4246de.png)](https://github.com/robotomize/fujes)
#### Another example
Grep is used for highlighting
```php
<?php
/**
 * `I want to find some airports =)`
 */
    print SearchFactory::find(
            'http://api.travelpayouts.com/data/airports.json ',
            'Sheremetievo',
            1,
            false
        )->fetchOne()['name'] . PHP_EOL;
    print SearchFactory::find(
            'http://api.travelpayouts.com/data/airports.json ',
            'Domogedov',
            1,
            false
        )->fetchOne()['en'] . PHP_EOL;
    print SearchFactory::find(
            'http://api.travelpayouts.com/data/airports.json ',
            'Yugnosahalinsk',
            1,
            false
        )->fetchOne()['en'] . PHP_EOL;
    print SearchFactory::find(
            'http://api.travelpayouts.com/data/airports.json ',
            'Puklovo',
            1,
            false
        )->fetchOne()['en'] . PHP_EOL;
```
[![Pic1](https://cloud.githubusercontent.com/assets/1207984/10570429/c6450f78-766e-11e5-8525-08ce6e91c8e9.png)](https://github.com/robotomize/fujes)

#### With full options
```php
<?php

use robotomize\Fujes\SearchFacade;
use robotomize\Fujes\SearchFactory;
// With factory
print SearchFactory::createSearchEngine(
    '/path/to/jsonurl',
    'What are searching for string',
    1,
    true, 
    false, 
    1,  
    'master' 
)->fetchOne() . PHP_EOL;  

print SearchFactory::createSearchEngine(
    '/path/to/jsonurl',
    'What are searching for string',
    1,
    true, 
    true, 
    1,  
    'master' 
)->fetchFew(3) . PHP_EOL;
```
### Documentation for Facttory && Facade
## Parameters
- path to json file '/go/to/path/name.json' or 'http://myapi/1.json'
- search line. 'search string'
- the depth of the array. (1-..) . Nesting output array. You will use a value of 1 or 2 the most.
- Display in json or PHP array. Output back to JSON?(true, false)
- Fetch one or more results. Get a set of results? Put true if you need to bring some results.
- Quality, 1 by default. @deprecated, but using. 1 default
- Version, master or dev. If you put the dev logs will be written about the exclusion or successful and not successful recognition. Once you see all the exceptions that fall.

## Usage with example.php
#### Basic examples you can try that.
TThese examples work if you do 
* git clone https://github.com/robotomize/fujes.git
* php -q src/example.php

```php
php -q src/example.php
```

#### Fetch one entry
```php
<?php

use robotomize\Fujes\SearchFacade;
use robotomize\Fujes\SearchFactory;

/**
 * Helper options. Search into biographical-directory-footnotes.json.
 * Match string Christensen
 * Output encode to json
 */
$options = [
    'json_file_name' => __DIR__ . '/data/biographical-directory-footnotes.json', 
    'search_string' => 'Christensen', 
    'depth_into_array' => '1', 
    'output_json' => true,
    'multiple_result' => false, 
    'search_quality' => 1, 
    'version' => 'dev' 
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

print $searchObject->fetchOne();
/**
 * Output this
 *
 * {"item":"Donna Christian-Green, St. Croix ","note":"[5125:
 * Biographical information under Donna Marie Christian Christensen. ]","line":56939}
 */
```
[![Pic1](https://cloud.githubusercontent.com/assets/1207984/10567879/e3c2ad78-7649-11e5-8282-3399410c6d30.png)](https://github.com/robotomize/fujes)
#### Next, fetch few entries. 
```php
<?php

/**
 * Get exception
 */
try {
    print $searchObject->fetchFew(3) . PHP_EOL;
} catch (\Exception $ex) {
    print $ex->getMessage() . PHP_EOL; 
    /**
    * Output this exception
    * multipleResult flag off, use $this->setMultipleResult(true)
    * and call this function again
    */
}
```
#### Set up $multipleResult = trueand everything will be fine.

```php
$searchObject->setMultipleResult(true);

/**
 * And this work
 */
print $searchObject->fetchFew(3) . PHP_EOL;
```
[![Pic1](https://cloud.githubusercontent.com/assets/1207984/10567890/14cb427c-764a-11e5-8f6f-06caa51dd2fa.png)](https://github.com/robotomize/fujes)
### Factory
```php
<?php
/**
 * The following example, you can use the factory.
 */
print SearchFactory::createSearchEngine(
    __DIR__ . '/../src/data/cities.json',
    'vladvostk',
    2,
    false,
    false,
    1,
    'dev'
)->fetchOne()['name'] . PHP_EOL;

print SearchFactory::createSearchEngine(
    __DIR__ . '/../src/data/cities.json',
    'Mosco',
    1,
    true,
    false,
    1,
    'dev'
)->fetchOne() . PHP_EOL;
```
[![Pic1](https://cloud.githubusercontent.com/assets/1207984/10567893/1e64ed6a-764a-11e5-8890-a24729be8843.png)](https://github.com/robotomize/fujes)

#### Another factory example
```php
<?php

print SearchFactory::createSearchEngine(
        __DIR__ . '/data/biographical-directory-footnotes.json',
        'linkoln',
        1,
        true,
        true,
        1,
        'dev'
    )->fetchFew(6) . PHP_EOL; 
```
Grep is used for highlighting
[![Pic1](https://cloud.githubusercontent.com/assets/1207984/10567892/1deb0220-764a-11e5-8088-071e2ea73822.png)](https://github.com/robotomize/fujes)

## License
Satis is licensed under the MIT License - see the LICENSE file for details


# fujes - PHP Fuzzy JSON search
[![Build Status](https://travis-ci.org/robotomize/fujes.svg)](https://travis-ci.org/robotomize/fujes/)
[![Code Climate](https://codeclimate.com/github/robotomize/fujes/badges/gpa.svg)](https://codeclimate.com/github/robotomize/fujes)

## Why?
Firstly, it is the implementation of the search on the format of the data in PHP. 
You can look up information on the fly. You can look for anything to JSON files. 
This is useful when your service accesses a different API.

The basis of the algorithm is taken Levenshtein.

## Installation
```sh
composer require robotomize/fujes dev-master
```
#### or
```sh
git clone https://github.com/robotomize/fujes.git
```
## Usage
```php
use robotomize\Fujes\SearchFacade;
use robotomize\Fujes\SearchFactory;
// With factory
print SearchFactory::createSearchEngine(
    '/path/to/jsonurl',
    'What are searching for string',
    1,
    true, // output to json?
    false, // multiple results?
    1,  //quality, 1 better
    'master' // master or dev. Dev saved logs
)->fetchOne() . PHP_EOL;    // print

print SearchFactory::createSearchEngine(
    '/path/to/jsonurl',
    'What are searching for string',
    1,
    true, // output to json?
    true, // multiple results?
    1,  //quality, 1 better
    'master' // master or dev. Dev saved logs
)->fetchFew(3) . PHP_EOL;    // count results

```

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

use robotomize\Fujes\SearchFacade;
use robotomize\Fujes\SearchFactory;

/**
 * Helper options. Search into biographical-directory-footnotes.json.
 * Match string Christensen
 * Output encode to json
 */
$options = [
    'json_file_name' => __DIR__ . '/data/biographical-directory-footnotes.json', // json file
    'search_string' => 'Christensen',                         // match string
    'depth_into_array' => '1',                              // depth into output
    'output_json' => true,                              // encode to json or output php array
    'multiple_result' => false,                     // multiple result or find one value?
    'search_quality' => 1,                        // 1 best quality search
    'version' => 'dev'                      // dev or master, logging exceptions && code event
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
[![Pic1](https://cloud.githubusercontent.com/assets/1207984/10523390/3097e5d8-73b5-11e5-9170-1d4f7086e711.png)](https://github.com/robotomize/fujes)
#### Next, fetch few entries. 
```php
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
### Parameters
* `path to json file`
* `search line`
* `the depth of the array`
* `Display in json and PHP array.`
* **`Fetch one or more results.`**
* `Quality, 1 by default`
* `Version, master or dev`
#### Set up $multipleResult = trueand everything will be fine.
```php
$searchObject->setMultipleResult(true);

/**
 * And this work
 */
print $searchObject->fetchFew(3) . PHP_EOL;
```
[![Pic1](https://cloud.githubusercontent.com/assets/1207984/10519592/51a6c5dc-73a1-11e5-9e03-eb8ef3aff6fe.png)](https://github.com/robotomize/fujes)
### Factory
```php
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
[![Pic1](https://cloud.githubusercontent.com/assets/1207984/10519608/6197b29e-73a1-11e5-9271-42920e2cb7c5.png)](https://github.com/robotomize/fujes)

#### Another factory example
```php
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
[![Pic1](https://cloud.githubusercontent.com/assets/1207984/10523574/08f4ddc8-73b6-11e5-9e56-231f45fd597e.png)](https://github.com/robotomize/fujes)
### Documentation
- `Depth` - ...

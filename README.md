# fujes - PHP Fuzzy JSON search
[![Latest Stable Version](https://poser.pugx.org/robotomize/fujes/v/stable)](https://packagist.org/packages/robotomize/fujes)
[![Total Downloads](https://poser.pugx.org/robotomize/fujes/downloads)](https://packagist.org/packages/robotomize/fujes)
[![License](https://poser.pugx.org/robotomize/fujes/license)](https://packagist.org/packages/robotomize/fujes)
[![Build Status](https://travis-ci.org/robotomize/fujes.svg)](https://travis-ci.org/robotomize/fujes/)
[![Code Climate](https://codeclimate.com/github/robotomize/fujes/badges/gpa.svg)](https://codeclimate.com/github/robotomize/fujes)

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
use robotomize\Fujes\SearchFactory;

/**
* 
* `I want to find some planes.`
*/
print SearchFactory::find('http://api.travelpayouts.com/data/planes.json ', 'Tu')->fetchOne() . PHP_EOL;
print SearchFactory::find('http://api.travelpayouts.com/data/planes.json ', 'Boing 7')->fetchOne() . PHP_EOL;
print SearchFactory::find('http://api.travelpayouts.com/data/planes.json ', 'An24')->fetchOne() . PHP_EOL;
```
[![Pic1](https://cloud.githubusercontent.com/assets/1207984/10540011/222c4248-743f-11e5-9a6e-876daac97e40.png)](https://github.com/robotomize/fujes)
#### Another example
Grep is used for highlighting
```php
/**
 * `I want to find some airports =)`
 */
print SearchFactory::find('http://api.travelpayouts.com/data/airports.json ', 'Sheremetievo')->fetchOne() . PHP_EOL;
print SearchFactory::find('http://api.travelpayouts.com/data/airports.json ', 'Domogedov')->fetchOne() . PHP_EOL;
print SearchFactory::find('http://api.travelpayouts.com/data/airports.json ', 'Yugnosahalinsk')->fetchOne() . PHP_EOL;
print SearchFactory::find('http://api.travelpayouts.com/data/airports.json ', 'Puklovo')->fetchOne() . PHP_EOL;
```
[![Pic1](https://cloud.githubusercontent.com/assets/1207984/10540250/5b28cd9e-7441-11e5-9b11-1cac94a2d7e7.png)](https://github.com/robotomize/fujes)

#### With full options
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
[![Pic1](https://cloud.githubusercontent.com/assets/1207984/10539054/cdabef54-7437-11e5-90c1-cc59ded176dd.png)](https://github.com/robotomize/fujes)
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
[![Pic1](https://cloud.githubusercontent.com/assets/1207984/10539171/be08a8ca-7438-11e5-92c1-b68fd652b430.png)](https://github.com/robotomize/fujes)

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
Grep is used for highlighting
[![Pic1](https://cloud.githubusercontent.com/assets/1207984/10539194/dc399700-7438-11e5-9c30-0223a18ce380.png)](https://github.com/robotomize/fujes)
### Documentation
- `Depth` - ...
## License
Satis is licensed under the MIT License - see the LICENSE file for details


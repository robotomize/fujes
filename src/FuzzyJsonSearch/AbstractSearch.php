<?php

namespace FuzzyJsonSearch;

/**
 * Class AbstractSearch
 * Expand this class
 * You can search for another class that implements only the search can be inherited and expanded.
 *
 * @package jsonSearch
 * @author  robotomize@gmail.com
 */
abstract class AbstractSearch
{
    abstract public function search($inputArray, $key, $level);
}

<?php

namespace jsonSearch;

/**
 * Class AbstractSearch
 * @package jsonSearch
 * @author robotomize@gmail.com
 */
abstract class AbstractSearch
{
    abstract public function search($inputArray, $key, $level);
}
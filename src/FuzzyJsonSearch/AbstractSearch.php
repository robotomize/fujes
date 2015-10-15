<?php
/**
 * This file is part of the FuJaySearch package.
 * @link    https://github.com/robotomize/FuJaySearch
 * @license http://www.opensource.org/licenses/mit-license.php MIT (see the LICENSE file)
 */

namespace FuzzyJsonSearch;

/**
 * Class AbstractSearch
 * Include && extends searching algorithm this
 * Expand this class
 * You can search for another class that implements only the search can be inherited and expanded.
 *
 * @package FuJaySearch
 * @author  robotomize@gmail.com
 */
abstract class AbstractSearch
{
    abstract public function search($inputArray, $key, $level);
}

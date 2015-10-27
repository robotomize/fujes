<?php
    /**
     * This file is part of the Fujes package.
     * @link    https://github.com/robotomize/fujes
     * @license http://www.opensource.org/licenses/mit-license.php MIT (see the LICENSE file)
     */

namespace robotomize\Fujes;

/**
 * Interface ISearch
 * Implement searching algorithm this
 * You can search for another class that implements only the search can be inherited and expanded.
 *
 * @package robotomize\Fujes
 * @author  robotomize@gmail.com
 */
interface ISearch
{
    /**
     * @param array $inputArray
     * @param string $key
     * @param string $level
     *
     * @return mixed
     */
    public function search($inputArray, $key, $level);
}

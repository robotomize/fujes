<?php
    /**
     * This file is part of the Fujes package.
     * @link    https://github.com/robotomize/fujes
     * @license http://www.opensource.org/licenses/mit-license.php MIT (see the LICENSE file)
     */
namespace robotomize\Utils;

/**
 * Factory for Status class
 * Get status processing in %
 * Class GetStatus
 * @package robotomize\Utils
 * @author robotomize@gmail.com
 */
class GetStatus
{
    public static function getStatus($current, $count)
    {
        return new Status($current, $count);
    }
}

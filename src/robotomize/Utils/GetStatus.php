<?php

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

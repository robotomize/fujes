<?php
    /**
     * This file is part of the Fujes package.
     * @link    https://github.com/robotomize/fujes
     * @license http://www.opensource.org/licenses/mit-license.php MIT (see the LICENSE file)
     */
namespace robotomize\Utils;

use robotomize\Exceptions\FileNotFoundException;

/**
 * Class Json utils
 * @package robotomize\Utils
 * @author robotomize@gmail.com
 */
class Json implements IUtils
{
    /**
     * @param string $string
     * @param bool $assoc
     * @param bool $fixNames
     *
     * @return mixed
     */
    public static function jsonDecode($string, $assoc = true, $fixNames = true)
    {
        if (strpos($string, '(') === 0) {
            $string = substr($string, 1, strlen($string) - 2);
        }

        if ($fixNames) {
            $string = preg_replace("/(?<!\"|'|\w)([a-zA-Z0-9_]+?)(?!\"|'|\w)\s?:/", "\"$1\":", $string);
        }

        return json_decode($string, $assoc);
    }

    /**
     * @param string $string
     *
     * @return bool
     */
    public static function isJsonTest($string)
    {
        $string = substr($string, 0, 255);
        if (json_last_error() == JSON_ERROR_NONE) {
            if (substr($string, 0, 1) === '[') {
                return true;
            } elseif (substr($string, 0, 1) === '{') {
                return true;
            } else {
                return false;
            }
        }
        return false;
    }

    /**
     * If the flag is set, then encode the output array in json
     *
     * @param array $needleBranch
     * @param array $relevantTree
     * @param bool $encodeFlag
     *
     * @return string
     */
    public static function jsonEncode($needleBranch, $relevantTree, $encodeFlag = true)
    {
        if (0 !== count($needleBranch)) {
            return $encodeFlag ? json_encode($needleBranch) : $needleBranch;
        } else {
            return $encodeFlag ? json_encode($relevantTree) : $relevantTree;
        }
    }
}


<?php
    /**
     * This file is part of the Fujes package.
     * @link    https://github.com/robotomize/fujes
     * @license http://www.opensource.org/licenses/mit-license.php MIT (see the LICENSE file)
     */

namespace robotomize\Fujes;

/**
 * Class SearchLevenshteinCompare
 * Class based on Levenshtein compare 2 strings
 * @package robotomize\Fujes
 * @author  robotomize@gmail.com
 * @version 0.4.1.0
 */
class SearchLevenshteinCompare implements ISearch
{
    /**
     * @var array
     */
    private $inputArray = [];

    /**
     * @var string
     */
    private $matchString = '';

    /**
     * @var boolean
     */
    private $multipleResult;

    /**
     * @var int
     */
    private $quality;

    /**
     * @var
     */
    private $versionType;

    /**
     * @param array $inputArray
     * @param string $matchString
     * @param bool $multipleResult
     * @param int $quality
     * @param string $versionType
     */
    public function __construct(
        $inputArray,
        $matchString,
        $multipleResult = false,
        $quality = 1,
        $versionType = 'master'
    ) {
        if (0 === count($inputArray) || $matchString === '') {
            throw new \InvalidArgumentException;
        } else {
            $this->inputArray = $inputArray;
            $this->matchString = mb_strtolower($matchString);
            $this->multipleResult = $multipleResult;
            $this->quality = $quality;
            $this->precision = $this->quality;
            $this->exitCoefficient = $this->calculateExitCoefficient($this->matchString);
            $this->versionType = $versionType;
        }
    }

    /**
     * @var int
     */
    private static $directMatchCoefficient = 0;

    /**
     * @var array
     */
    private $directMatch = [];

    /**
     * Direct comparison for equality.
     *
     * @param $current
     *
     * @return boolean
     */
    public function directCompareTwoString($current)
    {
        if (strtolower(trim($current)) === $this->matchString) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param array $sheet
     * @param string $keys
     *
     * @return bool
     */
    private function splitDirectMatchSheetJsonTree($sheet, $keys)
    {
        $variants = explode(' ', $sheet);
        foreach ($variants as $val) {
            $temp = $this->directCompareTwoString($val, $keys);
            if ($temp === true) {
                $this->directMatch[] = [$keys, $sheet, 0];
                return true;
            }
        }

        return false;
    }

    /**
     * @var int
     */
    private $countDepth = 0;

    /**
     * @param array $inputArray
     */
    public function countDepth($inputArray = [])
    {
        if (0 === count($inputArray)) {
            $inputArray = $this->inputArray;
        }
        foreach ($inputArray as $kk => $vv) {
            /**
             * Check came an array or a string, if the string, then compare with the unknown.
             * If you receive an array, calls itself recursively.
             */
            if (is_array($vv)) {
                $this->countDepth($vv);
            } else {
                continue;
            }
            $this->countDepth++;
        }
        return true;
    }

    /**
     * Function for preliminary passage through the tree.
     *
     * @param array  $inputArray
     * @param string $key
     * @param int    $level
     *
     * @return bool
     */
    public function preSearch($inputArray = [], $key = '', $level = 0)
    {
        if (0 === count($inputArray)) {
            $inputArray = $this->inputArray;
        }

        foreach ($inputArray as $kk => $vv) {
            /**
             * Check came an array or a string, if the string, then compare with the unknown.
             * If you receive an array, calls itself recursively.
             */
            $keys = $key !== '' ? sprintf('%s,%s', $key, $kk) : $kk;
            if (is_array($vv)) {
                $this->preSearch($vv, $keys, $level);
            } else {
                if ($this->splitDirectMatchSheetJsonTree($vv, $keys)) {
                    break;
                } else {
                    continue;
                }
            }
        }

        if (0 !== count($this->directMatch)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @var int
     */
    private $precision;

    /**
     * @param $current
     * @param string $key
     *
     * @return int
     */
    public function compareStart($current, $key)
    {
        $compare = levenshtein(strtolower(trim($current)), $this->matchString);
        if (strtolower($current) === $this->matchString || $compare <= $this->precision) {
            $this->directMatch[] = [$key, $current, $compare];
            return $compare;
        } else {
            return $compare;
        }
    }

    /**
     * @var double
     */
    private $exitCoefficient;

    /**
     * @param string $string
     *
     * @return double
     */
    private function calculateExitCoefficient($string)
    {
        return strlen($this->matchString) - floor(strlen($string) / 2);
    }

    /**
     * Split current sheet
     *
     * @param array $sheet
     * @param string $keys
     *
     * @return array
     */
    private function splitSheetJsonTree($sheet, $keys)
    {
        $variants = explode(' ', $sheet);
        $iterator = 0;
        $relevantResult = [];
        foreach ($variants as $val) {
            $currentValue = $this->compareStart($val, $keys);

            if ($iterator === 0) {
                $relevantResult = $currentValue;
            }

            if ((int) $currentValue < (int) $relevantResult) {
                $relevantResult = (int) $currentValue;
            }
            $iterator++;
        }
        return $relevantResult < $this->exitCoefficient ? [$keys, $sheet, $relevantResult] : [];
    }

    /**
     * @var int
     */
    private $searchIteration = 0;

    /**
     * Recursive array converted from json
     *
     * @param array $inputArray
     * @param string $key
     *
     * @return bool
     */
    public function search($inputArray = [], $key = '', $level = 0)
    {
        if (0 === count($inputArray)) {
            $inputArray = $this->inputArray;
        }

        foreach ($inputArray as $kk => $vv) {
            /**
             * Check came an array or a string, if the string, then compare with the unknown.
             * If you receive an array, calls itself recursively.
             */
            $keys = $key !== '' ? sprintf('%s,%s', $key, $kk) : $kk;
            if (is_array($vv)) {
                $this->search($vv, $keys, $level);
            } else {
                $currentCompareArray = $this->splitSheetJsonTree($vv, $keys);
                if (0 !== count($currentCompareArray)) {
                    $this->scoreMatrix[] = $currentCompareArray;
                }
                if (0 !== count($this->directMatch) && !$this->multipleResult) {
                    break;
                }
            }
        }
        if (0 !== count($this->scoreMatrix)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @var array
     */
    private $scoreMatrix = [];

    /**
     * @var array
     */
    private $sortingArray = [];

    /**
     * @var array
     */
    private $sortingPriorArray = [];

    /**
     * It is necessary to generate an array of reference on the basis of race Levenshtein
     * @return array
     */
    private function generateSortArray()
    {
        foreach ($this->scoreMatrix as $vv) {
            $this->sortingArray[] = $vv[2];
        }
    }

    /**
     * This method sorts the resulting array of distance.
     *
     * @return bool
     */
    private function sortingScoreMatrix()
    {
        $this->generateSortArray();
        if (0 !== count($this->scoreMatrix)) {
            array_multisort($this->sortingArray, SORT_DESC, $this->scoreMatrix, SORT_ASC);
        } else {
            return false;
        }
        return true;
    }

    /**
     * @return array
     */
    public function relevantCalc()
    {
        if (0 !== count($this->scoreMatrix)) {
            if ($this->sortingScoreMatrix()) {
                return $this->scoreMatrix;
            } else {
                return [];
            }
        }
    }

    /**
     * @return string
     */
    public function __toString()
    {
        if (0 !== count($this->scoreMatrix)) {
            return serialize($this->scoreMatrix);
        } else {
            return serialize($this);
        }
    }

    /**
     * @return $this|array
     */
    public function __invoke()
    {
        if (0 !== count($this->scoreMatrix)) {
            return $this->scoreMatrix;
        } else {
            return $this;
        }
    }

    /**
     * @return array
     */
    public function getScoreMatrix()
    {
        return $this->scoreMatrix;
    }

    /**
     * @return array
     */
    public function getSortingArray()
    {
        return $this->sortingArray;
    }

    /**
     * @return integer[]
     */
    public function getCountArrays()
    {
        return [count($this->sortingArray), count($this->scoreMatrix)];
    }

    /**
     * @return string
     */
    public function getMatchString()
    {
        return $this->matchString;
    }

    /**
     * @param string $matchString
     */
    public function setMatchString($matchString)
    {
        $this->matchString = $matchString;
        $this->exitCoefficient = $this->calculateExitCoefficient($this->matchString);
    }

    /**
     * @return array
     */
    public function getInputArray()
    {
        return $this->inputArray;
    }

    /**
     * @param array $inputArray
     */
    public function setInputArray($inputArray)
    {
        $this->inputArray = $inputArray;
    }

    /**
     * @return array
     */
    public function getDirectMatch()
    {
        return $this->directMatch;
    }

    /**
     * @param array $scoreMatrix
     */
    public function setScoreMatrix($scoreMatrix)
    {
        $this->scoreMatrix = $scoreMatrix;
    }

    /**
     * @return int
     */
    public function getPrecision()
    {
        return $this->precision;
    }

    /**
     * @param int $precision
     */
    public function setPrecision($precision)
    {
        $this->precision = $precision;
    }

    /**
     * @return boolean
     */
    public function getMultipleResult()
    {
        return $this->multipleResult;
    }

    /**
     * @param boolean $resultsCount
     */
    public function setMultipleResult($resultsCount)
    {
        $this->multipleResult = $resultsCount;
    }

    /**
     * @return int
     */
    public static function getDirectMatchCoefficient()
    {
        return self::$directMatchCoefficient;
    }

    /**
     * @param int $directMatchCoefficient
     */
    public static function setDirectMatchCoefficient($directMatchCoefficient)
    {
        self::$directMatchCoefficient = $directMatchCoefficient;
    }

    /**
     * @return array
     */
    public function getSortingPriorArray()
    {
        return $this->sortingPriorArray;
    }

    /**
     * @param array $sortingPriorArray
     */
    public function setSortingPriorArray($sortingPriorArray)
    {
        $this->sortingPriorArray = $sortingPriorArray;
    }

    /**
     * @return int
     */
    public function getCountDepth()
    {
        return $this->countDepth;
    }

    /**
     * @param int $countDepth
     */
    public function setCountDepth($countDepth)
    {
        $this->countDepth = $countDepth;
    }
}

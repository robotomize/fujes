<?php

namespace jsonSearch;

/**
 * Class RecursiveTraversal
 * @package jsonSearch
 * @author robotomzie@gmail.com
 * @version 0.3
 */
class SearchTreeWalk extends AbstractSearch
{
    /**
     * @var array
     */
    private $_inputArray = [];

    /**
     * @var string
     */
    private $_matchString = '';

    /**
     * @var boolean
     */
    private $_multipleResult;

    /**
     * @param $inputArray
     * @param $matchString
     */
    public function __construct($inputArray, $matchString, $multipleResult = false)
    {
        if (0 === count($inputArray) || $matchString === '') {
            throw new \InvalidArgumentException;
        } else {
            $this->_inputArray = $inputArray;
            $this->_matchString = $matchString;
            $this->_multipleResult = $multipleResult;
        }
    }

    /**
     * @var int
     */
    private static $_coefficient = 1.5;

    /**
     * @var int
     */
    private static $_directMatchCoefficient = 0;

    /**
     * @var array
     */
    private $_directMatch = [];

    /**
     * Direct comparison for equality.
     *
     * @param $current
     * @param $key
     *
     * @return array|bool
     */
    private function directCompareTwoString($current, $key)
    {
        if (strtolower($current) === $this->_matchString) {
            $this->_directMatch[] = [$key, $current, self::$_directMatchCoefficient];
            return true;
        } else {
            return false;
        }
    }

    /**
     * Function for preliminary passage through the tree.
     *
     * @param array $inputArray
     * @param string $key
     * @param int $level
     *
     * @return bool
     */
    public function preSearch($inputArray = [], $key = '', $level = 0)
    {
        if (0 === count($inputArray)) {
            $inputArray = $this->_inputArray;
        }

        foreach ($inputArray as $kk => $vv) {
            /**
             * Check came an array or a string, if the string, then compare with the unknown.
             * If you receive an array, calls itself recursively.
             */
            if (is_array($vv)) {
                $keys = $key !== '' ?  sprintf('%s,%s', $key, $kk) : $kk;
                $this->preSearch($vv, $keys, $level);
            } else {
                $keys = $key !== '' ?  sprintf('%s,%s', $key, $kk) : $kk;
                if(!$this->directCompareTwoString($vv, $keys)) {
                    continue;
                } else {
                    break;
                }
            }
        }

        if (0 !== count($this->_directMatch)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @var int
     */
    private static $_precision = 3;

    /**
     * @param $current
     * @param $key
     *
     * @return array
     */
    private function compareStart($current, $key)
    {
        $compare = levenshtein(strtolower($current), $this->_matchString);
        if (strtolower($current) === $this->_matchString || $compare <= self::$_precision) {
            $this->_directMatch[] = [$key, $current, $compare];
            return [$key, $current, $compare];
        } else {
            return [$key, $current, $compare];
        }
    }

    /**
     * Recursive array converted from json
     * @param $inputArray
     * @param string $key
     *
     * @return bool
     */
    public function search($inputArray = [], $key = '', $level = 0)
    {
        if (0 === count($inputArray)) {
            $inputArray = $this->_inputArray;
        }

        foreach ($inputArray as $kk => $vv) {
            /**
             * Check came an array or a string, if the string, then compare with the unknown.
             * If you receive an array, calls itself recursively.
             */
            if (is_array($vv)) {
                $keys = $key !== '' ?  sprintf('%s,%s', $key, $kk) : $kk;
                $this->search($vv, $keys, $level);
            } else {
                $keys = $key !== '' ?  sprintf('%s,%s', $key, $kk) : $kk;
                $this->_scoreMatrix[] = $this->compareStart($vv, $keys);
                if (0 !== count($this->_directMatch) && !$this->_multipleResult) {
                    break;
                }
            }
        }
        if (0 !== count($this->_scoreMatrix)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @var array
     */
    private $_scoreMatrix = [];

    /**
     * @var array
     */
    private $_sortingArray = [];

    /**
     * It is necessary to generate an array of reference on the basis of race Levenshtein
     * @return array
     */
    private function generateSortArray()
    {
        foreach ($this->_scoreMatrix as $vv) {
            $this->_sortingArray[] = $vv[2];
        }
        return $this->_sortingArray;
    }

    /**
     * This method sorts the resulting array of distance.
     * @return bool
     */
    private function sortingScoreMatrix()
    {
        if (0 !== $this->_scoreMatrix) {
            array_multisort($this->generateSortArray(), SORT_DESC, $this->_scoreMatrix, SORT_ASC);
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
        if (0 !== count($this->_scoreMatrix)) {
            if ($this->sortingScoreMatrix()) {
                return $this->_scoreMatrix;
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
        if (0 !== count($this->_scoreMatrix)) {
            return serialize($this->_scoreMatrix);
        } else {
            return serialize($this);
        }
    }

    /**
     * @return $this|array
     */
    public function __invoke()
    {
        if (0 !== count($this->_scoreMatrix)) {
            return $this->_scoreMatrix;
        } else {
            return $this;
        }
    }

    /**
     * @return array
     */
    public function getScoreMatrix()
    {
        return $this->_scoreMatrix;
    }

    /**
     * @return array
     */
    public function getSortingArray()
    {
        return $this->_sortingArray;
    }

    /**
     * @return array
     */
    public function getCountArrays()
    {
        return [count($this->_sortingArray), count($this->_scoreMatrix)];
    }

    /**
     * @return string
     */
    public function getMatchString()
    {
        return $this->_matchString;
    }

    /**
     * @param string $matchString
     */
    public function setMatchString($matchString)
    {
        $this->_matchString = $matchString;
    }

    /**
     * @return string
     */
    public function getMatch()
    {
        return $this->_matchString;
    }

    /**
     * @param string $match
     */
    public function setMatch($match)
    {
        $this->_matchString = $match;
    }

    /**
     * @return array
     */
    public function getInputArray()
    {
        return $this->_inputArray;
    }

    /**
     * @param array $inputArray
     */
    public function setInputArray($inputArray)
    {
        $this->_inputArray = $inputArray;
    }

    /**
     * @return array
     */
    public function getDirectMatch()
    {
        return $this->_directMatch;
    }

    /**
     * @param array $scoreMatrix
     */
    public function setScoreMatrix($scoreMatrix)
    {
        $this->_scoreMatrix = $scoreMatrix;
    }

    /**
     * @return int
     */
    public static function getPrecision()
    {
        return self::$_precision;
    }

    /**
     * @param int $precision
     */
    public static function setPrecision($precision)
    {
        self::$_precision = $precision;
    }

    /**
     * @return int
     */
    public function getMultipleResult()
    {
        return $this->_multipleResult;
    }

    /**
     * @param int $resultsCount
     */
    public function setMultipleResult($resultsCount)
    {
        $this->_multipleResult = $resultsCount;
    }

    /**
     * @return int
     */
    public static function getDirectMatchCoefficient()
    {
        return self::$_directMatchCoefficient;
    }

    /**
     * @param int $directMatchCoefficient
     */
    public static function setDirectMatchCoefficient($directMatchCoefficient)
    {
        self::$_directMatchCoefficient = $directMatchCoefficient;
    }
}
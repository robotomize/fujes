<?php

namespace jsonSearch;

require_once 'AbstractSearch.php';

/**
 * Class RecursiveTraversal
 * @package jsonSearch
 * @author robotomzie@gmail.com
 * @version 0.0.1
 */
class LevDistanceSearch extends AbstractSearch
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
     * @param $inputArray
     * @param $matchString
     */
    public function __construct($inputArray, $matchString)
    {
        if (0 === count($inputArray) || $matchString === '') {
            throw new \InvalidArgumentException;
        } else {
            $this->_inputArray = $inputArray;
            $this->_matchString = $matchString;
        }
    }

    /**
     * @param $current
     * @param $key
     *
     * @return array
     */
    private function compareStart($current, $key)
    {
        $compare = levenshtein(mb_strtolower($current), $this->_matchString);
        return [$key, $current, $compare];
    }

    /**
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

        foreach ($inputArray as $kkey => $values) {
            if (is_array($values)) {
                $keys = $key !== '' ?  sprintf('%s,%s', $key, $kkey) : $kkey;
                $this->search($values, $keys, $level);
            } else {
                $keys = $key !== '' ?  sprintf('%s,%s', $key, $kkey) : $kkey;
                $this->_scoreMatrix[] = $this->compareStart($values, $keys);
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
}
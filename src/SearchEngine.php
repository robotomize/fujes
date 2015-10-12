<?php

namespace jsonSearch;

require_once 'LevDistanceSearch.php';

/**
 * Class SearchEngine
 * @package jsonSearch
 * @author robotomzie@gmail.com
 * @version 0.0.1
 */
class SearchEngine
{
    /**
     * @var string
     */
    private $_urlName = '';

    /**
     * @var string
     */
    private $_matchString = '';

    /**
     * @param $urlName
     * @param $matchString
     */
    public function __construct($urlName, $matchString)
    {
        if ($urlName == '' || $matchString == '') {
            throw new \InvalidArgumentException;
        } else {
            $this->_urlName = $urlName;
            $this->_matchString = mb_strtolower($matchString);
        }
    }

    /**
     * @var string
     */
    private $_jsonData = '';

    /**
     * @var array
     */
    private $_jsonTree = [];

    /**
     * @var array
     */
    private $_errorStackTraces = [];

    /**
     * Parsing Json to array and that is all
     */
    private function parseJsonToArray()
    {
        try {
            $this->_jsonData = file_get_contents($this->_urlName);
            $this->_jsonTree = json_decode($this->_jsonData, true);
        } catch(\Exception $ex) {
            /**
             * Fast view Exceptions
             */
            print sprintf('U get exception in %s with message %s', $ex->getLine(), $ex->getMessage());
            $this->_errorStackTraces[] = [$ex->getCode(), $ex->getFile(), $ex->getLine(), $ex->getMessage(), $ex->getTraceAsString()];
        }
    }

    /**
     * @var array
     */
    private $_relevantTree = [];

    /**
     * @var array
     */
    private $_sortedScoreMatrix = [];

    /**
     * Main method
     */
    public function run()
    {
        $this->parseJsonToArray();
        $searchObj = new LevDistanceSearch($this->_jsonTree, $this->_matchString);
        /**
         * Calculating matrix with scores
         */
        $searchObj->search();
        $searchObj->relevantCalc();

        $this->_sortedScoreMatrix = $searchObj->getScoreMatrix();
    }

    /**
     * @return string
     */
    private function jsonEncode($relevantArray)
    {
        if (0 === count($relevantArray)) {
            $relevantArray = $this->_relevantTree;
        }

        $keysArray = explode(',', $relevantArray[0]);
        $keysArray = array_slice($keysArray, 0, count($keysArray) - 1);
        $needleBranch = [];
        foreach ($keysArray as $vv) {
            //print $vv . PHP_EOL;
            if (0 === count($needleBranch)) {
                $needleBranch = $this->_jsonTree[$vv];
            } else {
                $needleBranch = $needleBranch[$vv];
            }
        }
        if (0 !== count($needleBranch)) {
            return json_encode($needleBranch);
        }
    }

    /**
     * @return array|mixed
     */
    public function fetchOne()
    {
        $this->_relevantTree = array_pop($this->_sortedScoreMatrix);
        return $this->jsonEncode($this->_relevantTree);
    }

    /**
     * @var array
     */
    private $_moreRelevantJsonTrees = [];


    /**
     * @param int $count
     *
     * @return array
     */
    public function fetchFew($count = 1)
    {
        while ($count > 0) {
            $this->_relevantTree = array_pop($this->_sortedScoreMatrix);
            $this->_moreRelevantJsonTrees[] = $this->jsonEncode($this->_relevantTree);
            $count--;
        }
        return $this->_moreRelevantJsonTrees;
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
    public function getUrlName()
    {
        return $this->_urlName;
    }

    /**
     * @param string $urlName
     */
    public function setUrlName($urlName)
    {
        $this->_urlName = $urlName;
    }

    /**
     * @return array
     */
    public function getJsonTree()
    {
        return $this->_jsonTree;
    }

    /**
     * @param array $jsonTree
     */
    public function setJsonTree($jsonTree)
    {
        $this->_jsonTree = $jsonTree;
    }

    /**
     * @return array
     */
    public function getMoreRelevantJsonTrees()
    {
        return $this->_moreRelevantJsonTrees;
    }

    /**
     * @return array
     */
    public function getRelevantTree()
    {
        return $this->_relevantTree;
    }

    /**
     * @return array
     */
    public function getErrorStackTraces()
    {
        return $this->_errorStackTraces;
    }

    /**
     * @return string
     */
    public function getJsonData()
    {
        return $this->_jsonData;
    }

    /**
     * @return array
     */
    public function getScoreMatrix()
    {
        return $this->_sortedScoreMatrix;
    }

    /**
     * @return array|string
     */
    public function __toString()
    {
        if (0 === count($this->_relevantTree)) {
            return '';
        } else {
            return $this->jsonEncode($this->_relevantTree);
        }
    }

    /**
     * @return array|mixed
     */
    public function __invoke()
    {
        if (0 === count($this->_relevantTree)) {
            $this->run();
            return $this->fetchOne();
        } else {
            return $this->fetchOne();
        }
    }
}
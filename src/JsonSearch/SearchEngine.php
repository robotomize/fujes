<?php

namespace jsonSearch;

/**
 * Class FuzzyFuzzy Json Search Engine
 *
 * ** FuuJaySearch **
 *
 * This is the class that implements the FUZZY search json document. This is an approximate version.
 * You can use it to FUZZY search when receiving data from the mongo or external API.
 * Algorithm? Simple recursive pass on the document tree json.
 * The complexity of the algorithm, when passing the array O(n).
 * Use the glasses on the basis of metrics Levenshtein. The lower floor is gaining points the higher the rank.
 *
 * @package jsonSearch
 * @author robotomize@gmail.com
 * @version 0.3
 * @usage
 * $tt = new SearchEngine('http://uWtfAPI.json', 'Avengers', 1, true)
 * $tt->run();
 * print $tt->fetchOne();
 *
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
     * @var int
     */
    private $_depth = 0;

    /**
     * @var
     */
    private $_jsonEncode;

    /**
     * @var boolean
     */
    private $_multipleResult;

    /**
     * Search engine constructor
     *
     * @param $urlName          -> 'url like http://api.travelpayouts.com/data/cities.json'
     * @param $matchString      -> 'What we are looking for'
     * @param int $depth        -> 'Nesting depth of the resulting array. Standard 1, key => value'
     * @param bool $jsonEncode  -> 'Encode whether the result back in json or leave in an array php'
     */
    public function __construct($urlName, $matchString, $depth = 0, $jsonEncode = true, $multipleResult = false)
    {
        if ($urlName == '' || $matchString == '') {
            throw new \InvalidArgumentException;
        } else {
            $this->_urlName = $urlName;
            $this->_matchString = mb_strtolower($matchString);
            $this->_depth = $depth;
            $this->_jsonEncode = $jsonEncode;
            $this->_multipleResult = $multipleResult;
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
     * @var int
     */
    private $_rangeSortedMatrix = 0;

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
            print sprintf('You get exception in %s with message %s', $ex->getLine(), $ex->getMessage());

            $this->_errorStackTraces[] = [
                $ex->getCode(),
                $ex->getFile(),
                $ex->getLine(),
                $ex->getMessage(),
                $ex->getTraceAsString()
            ];
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

    public function preCompilationDirectMatch(SearchTreeWalk $searchObject)
    {
        $searchObject->preSearch();
        if (0 !== count($searchObject->getDirectMatch())) {
            $searchObject->setScoreMatrix($searchObject->getDirectMatch());
            $this->_sortedScoreMatrix = $searchObject->getScoreMatrix();
            $this->setRangeSortedMatrix(count($this->_sortedScoreMatrix));
            return true;
        } else {
            return false;
        }
    }

    /**
     * Main method
     */
    public function run()
    {
        $this->parseJsonToArray();
        $searchObj = new SearchTreeWalk($this->_jsonTree, $this->_matchString, $this->_multipleResult);

        if (!$this->_multipleResult) {
            $searchObj->preSearch();
        }

        if (0 !== count($searchObj->getDirectMatch())) {
            $searchObj->setScoreMatrix($searchObj->getDirectMatch());
            $this->_sortedScoreMatrix = $searchObj->getScoreMatrix();
            $this->setRangeSortedMatrix(count($this->_sortedScoreMatrix));
        } else {
            /**
             * Calculating matrix with scores
             */
            $searchObj->search();
            print 'tractor ' . $this->getMultipleResult() . PHP_EOL;
            if (0 !== count($searchObj->getDirectMatch()) && !$this->_multipleResult) {
                print 'mamka';
                $searchObj->setScoreMatrix($searchObj->getDirectMatch());
            } else {
                $searchObj->relevantCalc();
            }

            $this->_sortedScoreMatrix = $searchObj->getScoreMatrix();
            $this->setRangeSortedMatrix(count($this->_sortedScoreMatrix));
            print $this->getRangeSortedMatrix();
        }

    }

    /**
     * @param $relevantArray
     *
     * @return array
     */
    private function createResultArray($relevantArray)
    {
        if (0 === count($relevantArray)) {
            $relevantArray = $this->_relevantTree;
        }

        $keysArray = explode(',', $relevantArray[0]);

        if ($this->_depth === 0) {
            $depth = count($keysArray) - 1;
        } else {
            $depth = count($keysArray) - $this->_depth;
        }

        $keysArray = array_slice($keysArray, 0, $depth);
        $needleBranch = [];
        foreach ($keysArray as $vv) {
            if (0 === count($needleBranch)) {
                $needleBranch = $this->_jsonTree[$vv];
            } else {
                $needleBranch = $needleBranch[$vv];
            }
        }
        return $needleBranch;
    }

    /**
     *
     * @return string
     */
    private function jsonEncode($needleBranch)
    {
        if (0 !== count($needleBranch)) {
            return $this->_jsonEncode ? json_encode($needleBranch) : $needleBranch;
        } else {
            return $this->_jsonEncode ? json_encode($this->_relevantTree) : $this->_relevantTree;
        }
    }

    /**
     * Get only relevant search results.
     *
     * @return array|mixed
     */
    public function fetchOne()
    {
        $this->_relevantTree = array_pop($this->_sortedScoreMatrix);
        $branchArray = $this->createResultArray($this->_relevantTree);
        return $this->jsonEncode($branchArray);
    }

    /**
     * @var array
     */
    private $_moreRelevantJsonTreesOnArray = [];

    /**
     * @var string
     */
    private $_moreJsonTreesOnString = '';

    /**
     * Get a set of search results, specify the number yourself.
     *
     * @param int $count
     *
     * @return array|string
     */
    public function fetchFew($count = 1)
    {
        if (!$this->_multipleResult) {
            return $this->fetchOne();
        }

        if ($count > $this->_rangeSortedMatrix) {
            $count = $this->_rangeSortedMatrix;
        }
        while ($count > 0) {
            $this->_relevantTree = array_pop($this->_sortedScoreMatrix);
            $branchArray = $this->createResultArray($this->_relevantTree);
            if ($this->_jsonEncode == true) {
                $this->_moreJsonTreesOnString .= $this->jsonEncode($branchArray);
            } else {
                $this->_moreRelevantJsonTreesOnArray[] = $this->jsonEncode($branchArray);
            }

            $count--;
        }
        return $this->_jsonEncode ? $this->_moreJsonTreesOnString : $this->_moreRelevantJsonTreesOnArray;
    }

    /**
     * Get all search results
     *
     * @return array|string
     */
    public function fetchAll()
    {
        if (!$this->_multipleResult) {
            return $this->fetchOne();
        }

        $count = count($this->_sortedScoreMatrix);
        while ($count > 0) {
            $this->_relevantTree = array_pop($this->_sortedScoreMatrix);
            $branchArray = $this->createResultArray($this->_relevantTree);
            if ($this->_jsonEncode == true) {
                $this->_moreJsonTreesOnString .= $this->jsonEncode($branchArray);
            } else {
                $this->_moreRelevantJsonTreesOnArray[] = $this->jsonEncode($branchArray);
            }
            $count--;
        }
        return $this->_jsonEncode ? $this->_moreJsonTreesOnString : $this->_moreRelevantJsonTreesOnArray;
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
     * @param int $rangeSortedMatrix
     */
    public function setRangeSortedMatrix($rangeSortedMatrix)
    {
        $this->_rangeSortedMatrix = $rangeSortedMatrix;
    }

    /**
     * @return string
     */
    public function getMoreJsonTreesOnString()
    {
        return $this->_moreJsonTreesOnString;
    }

    /**
     * @return int
     */
    public function getRangeSortedMatrix()
    {
        return $this->_rangeSortedMatrix;
    }

    /**
     * @return array
     */
    public function getSortedScoreMatrix()
    {
        return $this->_sortedScoreMatrix;
    }

    /**
     * @return int
     */
    public function getDepth()
    {
        return $this->_depth;
    }

    /**
     * @return mixed
     */
    public function getJsonEncode()
    {
        return $this->_jsonEncode;
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
}
<?php
    /**
     * This file is part of the Fujes package.
     * @link    https://github.com/robotomize/fujes
     * @license http://www.opensource.org/licenses/mit-license.php MIT (see the LICENSE file)
     */

namespace robotomize\Fujes;

use robotomize\Exceptions\JsonNotRecognized;
use robotomize\Utils\Json;
use robotomize\Utils\Log;
use robotomize\Utils\ExceptionWrap;
use robotomize\Utils\Network;
use robotomize\Exceptions\FileNotFoundException;
use robotomize\Exceptions\MultipleResultException;
use InvalidArgumentException;

/**
 * Class PHP Fuzzy Json Search Engine
 *
 * ** Fujes **
 *
 * @TODO optimize all code
 * This is the class that implements the FUZZY search json document. This is an approximate version.
 * You can use it to FUZZY search when receiving data from the mongo or external API.
 * Algorithm? Simple recursive pass on the document tree json.
 *
 * The complexity of the algorithm, when passing the array O(n).
 * Use the glasses on the basis of metrics of Levenshtein. The lower floor is gaining points the higher the rank.
 *
 * @package robotomize\Fujes
 * @author  robotomize@gmail.com
 * @version 0.4.1.0
 *
 * @usage
 * $tt = new SearchEngine('http://uWtfAPI.json', 'Avengers', 1, true, false, 1, 'master')
 * $tt->run();
 * print $tt->fetchOne();
 */
class SearchEngine
{
    /**
     * @var string
     */
    private $urlName = '';

    /**
     * @var string
     */
    private $matchString = '';

    /**
     * @var int
     */
    private $depth;

    /**
     * @var
     */
    private $jsonEncode;

    /**
     * @var boolean
     */
    private $multipleResult;

    /**
     * @var int
     */
    private $quality;

    /**
     * @var string
     */
    private static $version = '0.4.1.0';

    /**
     * On off debug features
     * @var string
     */
    private $versionType;

    /**
     * @var
     */
    private $logger;

    /**
     * @var
     */
    private $exceptionObject;

    /**
     * @var int
     */
    private static $depthDefault = 1;

    /**
     * @var bool
     */
    private static $jsonEncodeDefault = true;

    /**
     * @var bool
     */
    private static $multipleResultDefault = false;

    /**
     * @var int
     */
    private static $qualityDefault = 1;

    /**
     * Search engine constructor
     *
     * @param string $urlName          -> 'url like http://api.travelpayouts.com/data/cities.json'
     * @param string $matchString      -> 'What we are looking for'
     * @param int $depth
     * $depth      -> 'Nesting depth of the resulting array. Standard 1, key => value'
     * @param bool $jsonEncode
     * $jsonEncode -> 'Encode whether the result back in json or leave in an array php'
     * @param bool $multipleResult              -> multiple result or no
     * @param int $quality -> quality search , 1 - strict search, 2, 3 less strict
     * @param string $versionType -> debug option. Dev or master.
     * The first option writes in logs all exceptions and successful search.
     */
    public function __construct(
        $urlName,
        $matchString,
        $depth = 1,
        $jsonEncode = true,
        $multipleResult = false,
        $quality = 1,
        $versionType = 'master'
    ) {
        if ($urlName == '' || $matchString == '') {
            throw new InvalidArgumentException;
        } else {
            $this->urlName = $urlName;
            $this->matchString = mb_strtolower($matchString);

            if (is_int($depth)) {
                $this->depth = $depth;
            } else {
                $this->depth = self::$depthDefault;
            }
            if (is_bool($jsonEncode)) {
                $this->jsonEncode = $jsonEncode;
            } else {
                $this->jsonEncode = self::$jsonEncodeDefault;
            }
            if (is_bool($multipleResult)) {
                $this->multipleResult = $multipleResult;
            } else {
                $this->multipleResult = self::$multipleResultDefault;
            }
            if (is_int($quality)) {
                $this->quality = $quality;
            } else {
                $this->quality = self::$qualityDefault;
            }

            $this->versionType = $versionType;

            $this->exceptionObject = new ExceptionWrap($this->versionType);

            $this->logger = new Log($this->versionType);
        }
    }

    /**
     * @var string
     */
    private $jsonData = '';

    /**
     * @var
     */
    private $jsonTree = [];

    /**
     * @var array
     */
    private $errorStackTraces = [];

    /**
     * @var int
     */
    private $rangeSortedMatrix = 0;

    /**
     * @var string
     */
    private $jsonErrorString = '';

    /**
     * Parsing Json to array and that is all
     */
    private function parseJsonToArray()
    {
        if (file_exists($this->urlName)) {
            $this->jsonData = file_get_contents($this->urlName);
        } else {
            $this->jsonData = Network::curlWrap($this->urlName);
            if (trim($this->jsonData) == '') {
                $this->exceptionObject->create('Input file not found');
                throw new FileNotFoundException('Input file not found');
            }
        }
        if (Json::isJsonTest($this->jsonData)) {
            $this->jsonTree = json_decode(trim($this->jsonData), true);
            $this->jsonErrorString = json_last_error_msg();
            if (json_last_error() > 0) {
                $this->jsonTree = Json::jsonDecode(trim($this->jsonData), true);
            }
        }
    }

    /**
     * @var array
     */
    private $relevantTree = [];

    /**
     * @var array
     */
    private $sortedScoreMatrix = [];

    /**
     * Function for preliminary passage through the tree.
     *
     * @param Search LevenshteinCompare $searchObject
     *
     * @return bool
     */
    public function preCompilationDirectMatch(SearchLevenshteinCompare $searchObject)
    {
        $searchObject->preSearch();

        if (0 !== count($searchObject->getDirectMatch())) {
            $searchObject->setScoreMatrix($searchObject->getDirectMatch());

            $this->sortedScoreMatrix = $searchObject->getScoreMatrix();
            $this->setRangeSortedMatrix(count($this->sortedScoreMatrix));

            return true;
        } else {
            return false;
        }
    }

    /**
     * @var array
     */
    private $directMatch = [];

    /**
     * @return array
     */
    public function getDirectMatch()
    {
        return $this->directMatch;
    }

    /**
     * @param array $directMatch
     */
    public function setDirectMatch($directMatch)
    {
        $this->directMatch = $directMatch;
    }

    /**
     * Main method
     */
    public function run()
    {
        $this->parseJsonToArray();
        if (0 === count($this->jsonTree)) {
            $this->exceptionObject->create('The data is not in JSON format');
            throw new JsonNotRecognized('The data is not in JSON format');
        }
        $searchObj = new SearchLevenshteinCompare(
            $this->jsonTree,
            $this->matchString,
            $this->multipleResult,
            $this->quality,
            $this->versionType
        );

        if (!$this->multipleResult) {
            $searchObj->preSearch();
        }

        if (0 !== count($searchObj->getDirectMatch())) {
            $searchObj->setScoreMatrix($searchObj->getDirectMatch());
            $this->sortedScoreMatrix = $searchObj->getScoreMatrix();
            $this->setRangeSortedMatrix(count($this->sortedScoreMatrix));
        } else {
            /**
             * Calculating matrix with scores
             */
            $searchObj->search();

            $this->setDirectMatch($searchObj->getDirectMatch());
            if (0 !== count($searchObj->getDirectMatch()) && !$this->multipleResult) {
                $searchObj->setScoreMatrix($searchObj->getDirectMatch());
            } else {
                $searchObj->relevantCalc();
            }

            $this->sortedScoreMatrix = $searchObj->getScoreMatrix();
            $this->setRangeSortedMatrix(count($this->sortedScoreMatrix));
        }
        if (0 !== count($this->sortedScoreMatrix)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Building a solid wood on the basis of the stored keys.
     *
     * @param $relevantArray
     *
     * @return array
     */
    private function createResultArray($relevantArray)
    {
        if (0 === count($relevantArray)) {
            $relevantArray = $this->relevantTree;
        }

        /**
         * Parse stored keys
         */
        $keysArray = explode(',', $relevantArray[0]);

        if ($this->depth === 0) {
            $depth = count($keysArray) - 1;
        } else {
            $depth = count($keysArray) - $this->depth;
        }

        $keysArray = array_slice($keysArray, 0, $depth);
        $needleBranch = [];
        /**
         * We are building up an array
         */
        foreach ($keysArray as $vv) {
            if (0 === count($needleBranch)) {
                $needleBranch = $this->jsonTree[$vv];
            } else {
                $needleBranch = $needleBranch[$vv];
            }
        }
        /**
         * We derive a tree whose depth pointers in parameters
         */
        return $needleBranch;
    }



    /**
     * @var string
     */
    private $errorMessage = ' FAIL! The desired value is not found in the json';

    /**
     * @var string
     */
    private $okMessage = ' OK! The desired value found in the json';

    /**
     * Get only relevant search results.
     *
     * @return string
     */
    public function fetchOne()
    {
        $this->relevantTree = array_pop($this->sortedScoreMatrix);
        $branchArray = $this->createResultArray($this->relevantTree);
        $result = Json::jsonEncode($branchArray, $this->relevantTree, $this->jsonEncode);
        /**
         * Logging section
         */
        if (empty($result)) {
            $this->logger->pushFlashMsg('error');
            return $this->errorMessage;
        } else {
            $this->logger->pushFlashMsg('info');
            return $result;
        }
    }

    /**
     * Output format
     *
     * @var array
     */
    private $moreRelevantJsonTreesOnArray = [];

    /**
     * Output format
     *
     * @var string
     */
    private $moreJsonTreesOnString = '';

    /**
     * @return array|string
     */
    private function outputInfo()
    {
        if ($this->jsonEncode) {
            if (empty($this->moreJsonTreesOnString)) {
                $this->logger->pushFlashMsg('error');
                return $this->errorMessage;
            } else {
                $this->logger->pushFlashMsg('info');
                return $this->moreJsonTreesOnString;
            }
        } else {
            if (0 === count($this->moreRelevantJsonTreesOnArray)) {
                $this->logger->pushFlashMsg('error');
                return $this->errorMessage;
            } else {
                $this->logger->pushFlashMsg('info');
                return $this->moreRelevantJsonTreesOnArray;
            }
        }
    }

    /**
     * @param int $count
     */
    private function generateOutputData($count)
    {
        while ($count > 0) {
            /**
             * Get max scored values from ranged stack
             */
            $this->relevantTree = array_pop($this->sortedScoreMatrix);
            $branchArray = $this->createResultArray($this->relevantTree);
            if ($this->jsonEncode === true) {
                $this->moreJsonTreesOnString .= Json::jsonEncode($branchArray, $this->relevantTree, $this->jsonEncode);
            } else {
                $this->moreRelevantJsonTreesOnArray[] =
                    Json::jsonEncode($branchArray, $this->relevantTree, $this->jsonEncode);
            }
            $count--;
        }
    }

    /**
     * Get a set of search results, specify the number yourself.
     *
     * @param int $count
     *
     * @return array|string
     */
    public function fetchFew($count = 1)
    {
        if (!$this->multipleResult) {
            /**
             * If multiple flag off fetchOne faster
             */
            if ($count > 1) {
                throw new MultipleResultException(
                    'multipleResult flag off, use $this->setMultipleResult(true) and call this function again'
                );
            } else {
                return $this->fetchOne();
            }
        }

        if ($count > $this->rangeSortedMatrix) {
            $count = $this->rangeSortedMatrix;
        }

        $this->generateOutputData($count);

        return $this->outputInfo();

    }

    /**
     * Get all search results
     *
     * @return array|string
     */
    public function fetchAll()
    {
        if (!$this->multipleResult) {
            throw new MultipleResultException(
                'multipleResult flag off, use $this->setMultipleResult(true) and call this function again'
            );
        }
        $count = count($this->sortedScoreMatrix);
        $this->generateOutputData($count);
        return $this->jsonEncode ? $this->moreJsonTreesOnString : $this->moreRelevantJsonTreesOnArray;
    }



    /**
     * @return string
     */
    public function __toString()
    {
        if (0 === count($this->relevantTree)) {
            return '';
        } else {
            return Json::jsonEncode($this->relevantTree, $this->relevantTree, $this->jsonEncode);
        }
    }

    /**
     * @return string
     */
    public function __invoke()
    {
        if (0 === count($this->relevantTree)) {
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
        return $this->matchString;
    }

    /**
     * @param string $matchString
     */
    public function setMatchString($matchString)
    {
        $this->matchString = $matchString;
    }

    /**
     * @return string
     */
    public function getUrlName()
    {
        return $this->urlName;
    }

    /**
     * @param string $urlName
     */
    public function setUrlName($urlName)
    {
        $this->urlName = $urlName;
    }

    /**
     * @return array
     */
    public function getJsonTree()
    {
        return $this->jsonTree;
    }

    /**
     * @param array $jsonTree
     */
    public function setJsonTree($jsonTree)
    {
        $this->jsonTree = $jsonTree;
    }

    /**
     * @return array
     */
    public function getRelevantTree()
    {
        return $this->relevantTree;
    }

    /**
     * @return array
     */
    public function getErrorStackTraces()
    {
        return $this->errorStackTraces;
    }

    /**
     * @return string
     */
    public function getJsonData()
    {
        return $this->jsonData;
    }

    /**
     * @return array
     */
    public function getScoreMatrix()
    {
        return $this->sortedScoreMatrix;
    }

    /**
     * @param int $rangeSortedMatrix
     */
    public function setRangeSortedMatrix($rangeSortedMatrix)
    {
        $this->rangeSortedMatrix = $rangeSortedMatrix;
    }

    /**
     * @return string
     */
    public function getMoreJsonTreesOnString()
    {
        return $this->moreJsonTreesOnString;
    }

    /**
     * @return int
     */
    public function getRangeSortedMatrix()
    {
        return $this->rangeSortedMatrix;
    }

    /**
     * @return array
     */
    public function getSortedScoreMatrix()
    {
        return $this->sortedScoreMatrix;
    }

    /**
     * @return int
     */
    public function getDepth()
    {
        return $this->depth;
    }

    /**
     * @return boolean
     */
    public function getJsonEncode()
    {
        return $this->jsonEncode;
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
     * @return string
     */
    public static function getVersion()
    {
        return self::$version;
    }

    /**
     * @return string
     */
    public function getVersionType()
    {
        return $this->versionType;
    }

    /**
     * @param string $versionType
     */
    public function setVersionType($versionType)
    {
        $this->versionType = $versionType;
    }

    /**
     * @return string
     */
    public function getErrorMessage()
    {
        return $this->errorMessage;
    }

    /**
     * @param string $errorMessage
     */
    public function setErrorMessage($errorMessage)
    {
        $this->errorMessage = $errorMessage;
    }

    /**
     * @return string
     */
    public function getOkMessage()
    {
        return $this->okMessage;
    }

    /**
     * @param string $okMessage
     */
    public function setOkMessage($okMessage)
    {
        $this->okMessage = $okMessage;
    }
}

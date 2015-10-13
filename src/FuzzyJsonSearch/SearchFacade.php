<?php

namespace FuzzyJsonSearch;

/**
 * Class FacadeJsonSearch
 * It's just a facade for the main class.
 * I think the use of such an interface is much simpler, however,
 * have the option of using factory or techniques to deal with directly.
 *
 * @package jsonSearch
 * @author  robotomize@gmail.com
 * @version 0.3
 */
class SearchFacade
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
     * Facade constructor
     *
     * @param $urlName          -> 'url like http://api.travelpayouts.com/data/cities.json'
     * @param $matchString      -> 'What we are looking for'
     * @param int
     * $depth      -> 'Nesting depth of the resulting array. Standard 1, key => value'
     * @param bool
     * $jsonEncode -> 'Encode whether the result back in json or leave in an array php'
     * @param bool
     */
    public function __construct($urlName, $matchString, $depth = 0, $jsonEncode = true, $multipleResult = false)
    {
        if ($urlName == '' || $matchString == '') {
            throw new \InvalidArgumentException;
        } else {
            $this->urlName = $urlName;
            $this->matchString = mb_strtolower($matchString);
            $this->depth = 0;
            $this->jsonEncode = $jsonEncode;
            $this->multipleResult = $multipleResult;
        }
    }

    /**
     * Get only relevant search results.
     *
     * @return array|string
     */
    public function fetchOne()
    {
        $jsonSearch = new SearchEngine(
            $this->urlName,
            $this->matchString,
            $this->depth,
            $this->jsonEncode
        );
        $jsonSearch->run();
        return $jsonSearch->fetchOne();
    }

    /**
     * Get a set of search results, specify the number yourself.
     *
     * @param $count
     *
     * @return array
     */
    public function fetchFew($count)
    {
        $jsonSearch = new SearchEngine(
            $this->urlName,
            $this->matchString,
            $this->depth,
            $this->jsonEncode,
            $this->multipleResult
        );
        $jsonSearch->run();
        return $jsonSearch->fetchFew($count);
    }

    /**
     * Get all search results
     *
     * @return array
     */
    public function fetchAll()
    {
        $jsonSearch = new SearchEngine(
            $this->urlName,
            $this->matchString,
            $this->depth,
            $this->jsonEncode,
            $this->multipleResult
        );
        $jsonSearch->run();
        return $jsonSearch->fetchAll();
    }

    /**
     * @return array|string
     */
    public function __invoke()
    {
        return $this->fetchOne();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        if ($this->jsonEncode == false) {
            return serialize($this->fetchOne());
        } else {
            return $this->fetchOne();
        }
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
     * @return int
     */
    public function getDepth()
    {
        return $this->depth;
    }

    /**
     * @param int $depth
     */
    public function setDepth($depth)
    {
        $this->depth = $depth;
    }

    /**
     * @return mixed
     */
    public function getJsonEncode()
    {
        return $this->jsonEncode;
    }

    /**
     * @param mixed $jsonEncode
     */
    public function setJsonEncode($jsonEncode)
    {
        $this->jsonEncode = $jsonEncode;
    }

    /**
     * @return int
     */
    public function getMultipleResult()
    {
        return $this->multipleResult;
    }

    /**
     * @param int $resultsCount
     */
    public function setMultipleResult($resultsCount)
    {
        $this->multipleResult = $resultsCount;
    }
}

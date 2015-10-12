<?php

namespace jsonSearch;

/**
 * Class FacadeJsonSearch
 * It's just a facade for the main class.
 * I think the use of such an interface is much simpler, however,
 * have the option of using factory or techniques to deal with directly.
 *
 * @package jsonSearch
 * @author robotomize@gmail.com
 * @version 0.2
 */
class SearchFacade
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
    private $_depth;

    /**
     * @var
     */
    private $_jsonEncode;

    /**
     * Facade constructor
     *
     * @param $urlName          -> 'url like http://api.travelpayouts.com/data/cities.json'
     * @param $matchString      -> 'What we are looking for'
     * @param int $depth        -> 'Nesting depth of the resulting array. Standard 1, key => value'
     * @param bool $jsonEncode  -> 'Encode whether the result back in json or leave in an array php'
     */
    public function __construct($urlName, $matchString, $depth = 0, $jsonEncode = true)
    {
        if ($urlName == '' || $matchString == '') {
            throw new \InvalidArgumentException;
        } else {
            $this->_urlName = $urlName;
            $this->_matchString = mb_strtolower($matchString);
            $this->_depth = 0;
            $this->_jsonEncode = $jsonEncode;
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
            $this->_urlName, $this->_matchString, $this->_depth, $this->_jsonEncode
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
            $this->_urlName, $this->_matchString, $this->_depth, $this->_jsonEncode
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
            $this->_urlName, $this->_matchString, $this->_depth, $this->_jsonEncode
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
        if ($this->_jsonEncode == false) {
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
     * @return int
     */
    public function getDepth()
    {
        return $this->_depth;
    }

    /**
     * @param int $depth
     */
    public function setDepth($depth)
    {
        $this->_depth = $depth;
    }

    /**
     * @return mixed
     */
    public function getJsonEncode()
    {
        return $this->_jsonEncode;
    }

    /**
     * @param mixed $jsonEncode
     */
    public function setJsonEncode($jsonEncode)
    {
        $this->_jsonEncode = $jsonEncode;
    }
}
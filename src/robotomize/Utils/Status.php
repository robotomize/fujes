<?php

namespace robotomize\Utils;

/**
 * Class Status
 * @package robotomize\Utils
 * @author robotomize@gmail.com
 */
class Status
{

    /**
     * @var int
     */
    private $cnt = 0;

    /**
     * @var int
     */
    private $curr = 0;

    /**
     * @param $curr
     * @param $cnt
     */
    public function __construct($curr, $cnt)
    {
        if ($curr === '' || $cnt === '') {
            throw new \InvalidArgumentException;
        } else {
            $this->curr = $curr;
            $this->cnt = $cnt;
        }
    }

    /**
     * @return string
     */
    public function viewStatusBar()
    {
        return sprintf('Now loading: %s%s', '%', round(($this->curr / $this->cnt) * 100, 1));
    }

    /**
     * @return int
     */
    public function getCnt()
    {
        return $this->cnt;
    }

    /**
     * @param int $cnt
     */
    public function setCnt($cnt)
    {
        $this->cnt = $cnt;
    }

    /**
     * @return int
     */
    public function getCurr()
    {
        return $this->curr;
    }

    /**
     * @param int $curr
     */
    public function setCurr($curr)
    {
        $this->curr = $curr;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->viewStatusBar();
    }

    /**
     * @return string
     */
    public function __invoke()
    {
        return $this->viewStatusBar();
    }
}

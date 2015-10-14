<?php


namespace Utils;


class ExWrap
{

    /**
     * @var array
     */
    private $errorStackTraces;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->errorStackTraces = [];
    }

    /**
     * @param \Exception $objEx
     */
    public function push(\Exception $objEx)
    {
        $this->errorStackTraces[] = [
            'code'   => $objEx->getCode(),
            'file'   => $objEx->getFile(),
            'line'   => $objEx->getLine(),
            'msg'    => $objEx->getMessage(),
            'string' => $objEx->getTraceAsString()
        ];
    }

    /**
     *
     */
    public function fetchAll()
    {
        foreach ($this->errorStackTraces as $vv) {
            print sprintf('code:    %s',    $vv['code'])        . PHP_EOL;
            print sprintf('file:    %s',    $vv['file'])        . PHP_EOL;
            print sprintf('line:    %s',    $vv['line'])        . PHP_EOL;
            print sprintf('msg:     %s',    $vv['msg'])         . PHP_EOL;
            print sprintf('string:  %s',    $vv['string'])      . PHP_EOL;
        }
    }

    /**
     * @return mixed
     */
    public function pop()
    {
        return array_pop($this->errorStackTraces);
    }

    /**
     * @return array
     */
    public function getErrorStackTraces()
    {
        return $this->errorStackTraces;
    }

    /**
     * @param array $errorStackTraces
     */
    public function setErrorStackTraces($errorStackTraces)
    {
        $this->errorStackTraces = $errorStackTraces;
    }

    public function __invoke()
    {
        // TODO: Implement __invoke() method.
    }

    public function __toString()
    {
        // TODO: Implement __toString() method.
    }

}
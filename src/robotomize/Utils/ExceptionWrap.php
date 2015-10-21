<?php
    /**
     * This file is part of the Fujes package.
     * @link    https://github.com/robotomize/fujes
     * @license http://www.opensource.org/licenses/mit-license.php MIT (see the LICENSE file)
     */

namespace robotomize\Utils;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Carbon\Carbon;

/**
 * Class ExWrap
 * Exception wrappers and extends \Exceptions in future
 * @package Utils
 * @author robotomize@gmail.com
 */
class ExceptionWrap
{

    /**
     * @var array
     */
    private $errorStackTraces;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var
     */
    private $versionType;

    /**
     * @param string $versionType
     */
    public function __construct($versionType = 'master')
    {
        $this->errorStackTraces = [];
        $this->logger = new Logger('Exceptions');
        $this->logger->pushHandler(new StreamHandler(__DIR__ . '/../data/logs/exceptions.log', Logger::DEBUG));
        $this->versionType = $versionType;
    }

    /**
     * @param string $exceptionString
     */
    public function create($exceptionString)
    {
        $ex = new \Exception($exceptionString);
        $this->push($ex);
        $this->saveToDisk($ex);
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
        if ($this->versionType === 'dev') {
            print sprintf(
                'Exception in %s, line %s with message %s',
                $objEx->getFile(),
                $objEx->getLine(),
                $objEx->getMessage()
            );
        }
    }

    /**
     *
     */
    public function fetchAll()
    {
        foreach ($this->errorStackTraces as $vv) {
            print sprintf('code: %s', $vv['code']) . PHP_EOL;
            print sprintf('file: %s', $vv['file']) . PHP_EOL;
            print sprintf('line: %s', $vv['line']) . PHP_EOL;
            print sprintf('msg: %s', $vv['msg']) . PHP_EOL;
            print sprintf('string: %s', $vv['string']) . PHP_EOL;
        }
    }

    /**
     * @var string
     */
    private static $loggerFileName = __DIR__ . '/../data/logs/exceptions.log';

    /**
     *
     * @param \Exception $objEx
     */
    public function saveToDisk(\Exception $objEx)
    {
        try {
            $this->logger->addError(Carbon::now()->toDateTimeString() . ' : ' . $objEx->getTraceAsString());
        } catch (\Exception $e) {
            $this->push($e);
        }
        $this->push($objEx);
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
    public function __invoke()
    {
        if (0 !== count($this->errorStackTraces)) {
            return $this->errorStackTraces;
        } else {
            return [];
        }
    }

    /**
     * @return string
     */
    public function __toString()
    {
        if (0 !== count($this->errorStackTraces)) {
            return serialize($this->errorStackTraces);
        } else {
            return 'Not found any exceptions';
        }
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
}

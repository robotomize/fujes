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
 * Class Log. Wrap for monolog/monolog.
 * @package Utils
 * @author robotomize@gmail.com
 */
class Log
{

    /**
     * @var
     */
    private $logger;

    private $versionType;

    public function __construct($versionType = 'master')
    {
        $this->versionType = $versionType;
        $this->logger = new Logger('Logging');
        $this->logger->pushHandler(new StreamHandler(__DIR__ . '/../data/logs/common.log', Logger::DEBUG));
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
     * Little logging system responses.
     *
     * @param $type
     *
     * @return bool
     */
    public function pushFlashMsg($type)
    {
        if ($this->versionType === 'dev') {
            return false;
        }

        $exceptionObject = new ExceptionWrap();

        try {
            if ($type === 'error') {
                $this->logger->addWarning(Carbon::now()->toDateTimeString() . ' : ' . $this->errorMessage);
            } else {
                $this->logger->addInfo(Carbon::now()->toDateTimeString() . ' : ' . $this->okMessage);
            }
            return true;

        } catch (\Exception $e) {
            /**
             * Debug section
             */
            if ($this->versionType === 'dev') {
                $dumpEx = sprintf('Monolog is down in %s with %s', $e->getLine(), $e->getMessage());
                $exceptionObject->push($e);
                print $e->getTraceAsString() . PHP_EOL;
            }

            $exceptionObject->saveToDisk($e);
        }
        return true;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return serialize($this->logger);
    }

    /**
     * @return Logger
     */
    public function __invoke()
    {
        return $this->logger;
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

<?php
/**
 * This file is part of the FuJaySearch package.
 * @link    https://github.com/robotomize/FuJaySearch
 * @license http://www.opensource.org/licenses/mit-license.php MIT (see the LICENSE file)
 */

spl_autoload_register(
    function ($className) {
        $className = ltrim($className, '\\');
        $fileName = '';
        if ($lastNsPos = strripos($className, '\\')) {
            $namespace = substr($className, 0, $lastNsPos);
            $className = substr($className, $lastNsPos + 1);
            $fileName = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
        }
        $fileName = __DIR__ . DIRECTORY_SEPARATOR . $fileName . $className . '.php';
        if (file_exists($fileName)) {
            include $fileName;
            return true;
        }
        return false;
    }
);

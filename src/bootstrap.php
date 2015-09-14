<?php
/**
 * @copyright 2010-2015 JTL-Software GmbH
 * @package jtl\Connector\Example
 */

require_once (__DIR__ . "/../vendor/autoload.php");

use jtl\Connector\Application\Application;
use jtl\Connector\Example\Connector;

$application = null;

try {
    $logDir = CONNECTOR_DIR . DIRECTORY_SEPARATOR . 'logs';
    if (!is_dir($logDir)) {
        mkdir($logDir);
        chmod($logDir, 0777);
    }

    // Connector instance
    $connector = Connector::getInstance();
    $application = Application::getInstance();
    $application->register($connector);
    $application->run();
} catch (\Exception $e) {
    if (is_object($application)) {
        $handler = $application->getErrorHandler()->getExceptionHandler();
        $handler($e);
    }
}

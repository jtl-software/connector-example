<?php
/**
 * @copyright 2010-2015 JTL-Software GmbH
 * @package Jtl\Connector\Example
 */
require_once dirname(__DIR__). "/bootstrap.php";

use jtl\Connector\Application\Application;
use Jtl\Connector\Example\Connector;

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

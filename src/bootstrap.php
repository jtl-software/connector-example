<?php
/**
 * @copyright 2010-2015 JTL-Software GmbH
 * @package jtl\Connector\Example
 */

require_once (__DIR__ . "/../vendor/autoload.php");

use jtl\Connector\Application\Application;
use jtl\Connector\Core\Rpc\RequestPacket;
use jtl\Connector\Core\Rpc\ResponsePacket;
use jtl\Connector\Core\Rpc\Error;
use jtl\Connector\Core\Http\Response;
use jtl\Connector\Example\Connector;

function exception_handler(\Exception $exception)
{
    $trace = $exception->getTrace();
    if (isset($trace[0]['args'][0])) {
        $requestpacket = $trace[0]['args'][0];
    }

    $error = new Error();
    $error->setCode($exception->getCode())
        ->setData("Exception: " . substr(strrchr(get_class($exception), "\\"), 1) . " - File: {$exception->getFile()} - Line: {$exception->getLine()}")
        ->setMessage($exception->getMessage());

    $responsepacket = new ResponsePacket();
    $responsepacket->setError($error)
        ->setJtlrpc("2.0");

    if (isset($requestpacket) && $requestpacket !== null && $requestpacket instanceof RequestPacket) {
        $responsepacket->setId($requestpacket->getId());
    }

    Response::send($responsepacket);
}

set_exception_handler('exception_handler');

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
    exception_handler($e);
}

<?php
/**
 * @copyright 2010-2015 JTL-Software GmbH
 * @package jtl\Connector\Example
 */

require_once (__DIR__ . "/../vendor/autoload.php");

use jtl\Connector\Application\Application;
use jtl\Connector\Core\Logger\Logger;
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

function error_handler($errno, $errstr, $errfile, $errline, $errcontext)
{
    $types = array(
        E_ERROR => array(Logger::ERROR, 'E_ERROR'),
        E_WARNING => array(Logger::WARNING, 'E_WARNING'),
        E_PARSE => array(Logger::WARNING, 'E_PARSE'),
        E_NOTICE => array(Logger::NOTICE, 'E_NOTICE'),
        E_CORE_ERROR => array(Logger::ERROR, 'E_CORE_ERROR'),
        E_CORE_WARNING => array(Logger::WARNING, 'E_CORE_WARNING'),
        E_CORE_ERROR => array(Logger::ERROR, 'E_COMPILE_ERROR'),
        E_CORE_WARNING => array(Logger::WARNING, 'E_COMPILE_WARNING'),
        E_USER_ERROR => array(Logger::ERROR, 'E_USER_ERROR'),
        E_USER_WARNING => array(Logger::WARNING, 'E_USER_WARNING'),
        E_USER_NOTICE => array(Logger::NOTICE, 'E_USER_NOTICE'),
        E_STRICT => array(Logger::NOTICE, 'E_STRICT'),
        E_RECOVERABLE_ERROR => array(Logger::ERROR, 'E_RECOVERABLE_ERROR'),
        E_DEPRECATED => array(Logger::INFO, 'E_DEPRECATED'),
        E_USER_DEPRECATED => array(Logger::INFO, 'E_USER_DEPRECATED')
    );

    if (isset($types[$errno])) {
        $err = "(" . $types[$errno][1] . ") File ({$errfile}, {$errline}): {$errstr}";
        Logger::write($err, $types[$errno][0], 'global');
    } else {
        Logger::write("File ({$errfile}, {$errline}): {$errstr}", Logger::ERROR, 'global');
    }
}

set_error_handler('error_handler', E_ALL);
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

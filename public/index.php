<?php
/**
 * @copyright 2010-2015 JTL-Software GmbH
 * @package Jtl\Connector\Example
 */
require_once dirname(__DIR__). "/bootstrap.php";

use Jtl\Connector\Core\Application\Application;
use Jtl\Connector\Core\Config\ConfigParameter;
use Jtl\Connector\Core\Config\ConfigSchema;
use Jtl\Connector\Example\Connector;

$application = null;

$configSchema = new ConfigSchema;
$configSchema->setParameter(new ConfigParameter("token", "string", true));
$configSchema->setParameter(new ConfigParameter("dbHost", "string", true));
$configSchema->setParameter(new ConfigParameter("dbName", "string", true));
$configSchema->setParameter(new ConfigParameter("dbUsername", "string", true));
$configSchema->setParameter(new ConfigParameter("dbPassword", "string", true));

// Connector instance
$connector = new Connector;
$application = new Application($connector, CONNECTOR_DIR, null, $configSchema);
$application->run();
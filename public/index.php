<?php
/**
 * @copyright 2010-2015 JTL-Software GmbH
 * @package Jtl\Connector\Example
 */
require_once dirname(__DIR__) . "/bootstrap.php";

use Jtl\Connector\Core\Application\Application;
use Jtl\Connector\Core\Config\ConfigParameter;
use Jtl\Connector\Core\Config\ConfigSchema;
use Jtl\Connector\Core\Config\FileConfig;
use Jtl\Connector\Example\Connector;

$application = null;

//Setting up a custom FileConfig passing the needed File
$config = new FileConfig(sprintf('%s/config/config.json', CONNECTOR_DIR));

//Setting up a custom config schema that checks the config file for the defined properties
$configSchema = new ConfigSchema;
$configSchema->setParameter(new ConfigParameter("token", "string", true));
$configSchema->setParameter(new ConfigParameter("db.host", "string", true));
$configSchema->setParameter(new ConfigParameter("db.name", "string", true));
$configSchema->setParameter(new ConfigParameter("db.username", "string", true));
$configSchema->setParameter(new ConfigParameter("db.password", "string", true));

//Instantiating the Connector class which holds information and acts like a Toolbox the the application
$connector = new Connector;

//Instantiating and starting the Application as the highest instance of the Connector passing every custom object as well as the connector object
$application = new Application($connector, CONNECTOR_DIR, $config, $configSchema);
$application->run();
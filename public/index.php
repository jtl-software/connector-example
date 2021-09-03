<?php

$connectorDir = dirname(__DIR__);

require_once $connectorDir . "/vendor/autoload.php";

use Jtl\Connector\Core\Application\Application;
use Jtl\Connector\Core\Config\ConfigParameter;
use Jtl\Connector\Core\Config\ConfigSchema;
use Jtl\Connector\Core\Config\FileConfig;
use Jtl\Connector\Example\Connector;

$application = null;

//Setting up a custom FileConfig passing the needed File
$config = new FileConfig(sprintf('%s/config/config.json', $connectorDir));

//Setting up a custom config schema which checks the config file for the defined properties
$configSchema = (new ConfigSchema)
    ->setParameter(new ConfigParameter("token", "string", true))
    ->setParameter(new ConfigParameter("db.host", "string", true))
    ->setParameter(new ConfigParameter("db.name", "string", true))
    ->setParameter(new ConfigParameter("db.username", "string", true))
    ->setParameter(new ConfigParameter("db.password", "string", true));

//Instantiating and starting the Application as the highest instance of the connector
$application = new Application($connectorDir, $config, $configSchema);

//The connector object keeps specific information about the endpoint implementation
$connector = new Connector();

$application->run($connector);
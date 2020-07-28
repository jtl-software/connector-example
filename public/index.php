<?php
/**
 * @copyright 2010-2015 JTL-Software GmbH
 * @package Jtl\Connector\Example
 */
require_once dirname(__DIR__). "/bootstrap.php";

use Jtl\Connector\Core\Application\Application;
use Jtl\Connector\Core\Config\ConfigParameter;
use Jtl\Connector\Core\Config\ConfigSchema;
use Jtl\Connector\Core\Config\FileConfig;
use Jtl\Connector\Core\Exception\ConfigException;
use Jtl\Connector\Example\Connector;
use Jtl\Connector\Example\Installer\Installer;

$application = null;
$config = new FileConfig(sprintf('%s/config/config.json', CONNECTOR_DIR));
$configSchema = new ConfigSchema;

$configSchema->setParameter(new ConfigParameter("token", "string", true));
//$configSchema->setParameter(new ConfigParameter("db", "object", true)); No param type 'object' yet

if(!$config->has("token")) {
    if($config->has("db")) {
        $installer = new Installer($config);
        $installer->run();
    } else {
        return;
    }
}

// Connector instance
$connector = new Connector;
$application = new Application($connector, CONNECTOR_DIR, $config, $configSchema);
$application->run();
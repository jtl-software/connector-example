<?php

namespace Jtl\Connector\Example;

use DI\Container;
use Jtl\Connector\Core\Authentication\TokenValidatorInterface;
use Jtl\Connector\Core\Config\ConfigSchema;
use Jtl\Connector\Core\Connector\ConnectorInterface;
use Jtl\Connector\Core\Mapper\PrimaryKeyMapperInterface;
use Jtl\Connector\Example\Authentication\TokenValidator;
use Jtl\Connector\Example\Installer\Installer;
use Jtl\Connector\Example\Mapper\PrimaryKeyMapper;
use Noodlehaus\ConfigInterface;
use PDO;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * Example Connector
 * @access public
 */
class Connector implements ConnectorInterface
{
    protected $config;
    protected $db;
    
    public function initialize(ConfigInterface $config, Container $container, EventDispatcher $eventDispatcher) : void
    {
        $this->config = $config;
        $this->db = $this->getDatabaseInstance();
        
        //Checks if the connector-token is set to control if the installing routine should be executed
        if (!$this->config->has("token")) {
            $installer = new Installer($this->db, ConfigSchema::CONNECTOR_DIR);
            $installer->run($config->get());
            
            $this->config->set("token", "123456789");
            $this->config->write();
        }
        
        //Passing the instantiated database object the the DI container so it can later be accessed by the controllers
        $container->set(PDO::class, $this->db);
    }
    
    public function getPrimaryKeyMapper() : PrimaryKeyMapperInterface
    {
        //Defining the PrimaryKeyMapper which is used to manage the links between WAWI and shop entities
        return new PrimaryKeyMapper($this->db);
    }
    
    public function getTokenValidator() : TokenValidatorInterface
    {
        //Defining the TokenValidator which is used to check the given token on an Auth call
        return new TokenValidator($this->config);
    }
    
    public function getControllerNamespace() : string
    {
        //Defining the ControllerNamespace which holds the controller classes for all entities so the can be found by the application
        return "Jtl\Connector\Example\Controller";
    }
    
    public function getEndpointVersion() : string
    {
        //Defining the connectors associated shop version
        return "0.1";
    }
    
    public function getPlatformVersion() : string
    {
        //Defining the connectors version
        return "1";
    }
    
    public function getPlatformName() : string
    {
        //Defining the connectors associated shop name using "Bulk" as the default name for all test-connectors
        return "Bulk";
    }
    
    private function getDatabaseInstance() : PDO
    {
        $dbParams = $this->config->get("db");
        
        $db = new PDO(
            sprintf("mysql:host=%s;dbname=%s", $dbParams["host"], "example_connector_db"),
            $dbParams["username"],
            $dbParams["password"]/*,
            [PDO::ERRMODE_EXCEPTION]*/
        );
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        return $db;
    }
}
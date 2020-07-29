<?php

namespace Jtl\Connector\Example;

use DI\Container;
use Jtl\Connector\Core\Authentication\TokenValidatorInterface;
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
        
        if (!$this->config->has("token")) {
            $installer = new Installer($this->config);
            $installer->run();
        }
    }
    
    public function getPrimaryKeyMapper() : PrimaryKeyMapperInterface
    {
        return new PrimaryKeyMapper($this->db);
    }
    
    public function getTokenValidator() : TokenValidatorInterface
    {
        return new TokenValidator($this->config);
    }
    
    public function getControllerNamespace() : string
    {
        return "Jtl\Connector\Example\Controller";
    }
    
    public function getEndpointVersion() : string
    {
        return "0.1";
    }
    
    public function getPlatformVersion() : string
    {
        return "1";
    }
    
    public function getPlatformName() : string
    {
        //Default name
        return "Bulk";
    }
    
    private function getDatabaseInstance() : PDO
    {
        $dbParams = $this->config->get("db");
        
        return new PDO(
            sprintf("mysql:host=%s;dbname=%s", $dbParams["host"], $dbParams["name"]),
            $dbParams["username"],
            $dbParams["password"]
        );
    }
}
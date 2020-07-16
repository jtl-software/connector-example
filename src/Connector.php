<?php

namespace Jtl\Connector\Example;

use DI\Container;
use Jtl\Connector\Core\Authentication\TokenValidatorInterface;
use Jtl\Connector\Core\Config\ConfigParameter;
use Jtl\Connector\Core\Config\ConfigSchema;
use Jtl\Connector\Core\Connector\ConnectorInterface;
use Jtl\Connector\Core\Exception\ConfigException;
use Jtl\Connector\Core\Mapper\PrimaryKeyMapperInterface;
use Jtl\Connector\Example\Authentication\TokenValidator;
use Jtl\Connector\Example\Mapper\PrimaryKeyMapper;
use Noodlehaus\ConfigInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * Example Connector
 *
 * @access public
 */
class Connector implements ConnectorInterface
{
    protected $checkToken;
    
    public function initialize(ConfigInterface $config, Container $container, EventDispatcher $eventDispatcher) : void
    {
        if (!$config->has('token')){
            $config->set("token", "123456789");
            $config->write();
        }
        
        //Config Schema Example
        $configSchema = new ConfigSchema;
        $configSchema->setParameter(new ConfigParameter("token", "string", true));
        $configSchema->validateConfig($config);
        
        $this->checkToken = $config->get("token");
    }
    
    public function getPrimaryKeyMapper() : PrimaryKeyMapperInterface
    {
        return new PrimaryKeyMapper;
    }

    public function getTokenValidator() : TokenValidatorInterface
    {
        return new TokenValidator($this->checkToken);
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
        return "ExampleShopSystem";
    }
}
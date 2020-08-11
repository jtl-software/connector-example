<?php

namespace DemoPlugin;

use DI\Container;
use Jtl\Connector\Core\Definition\Action;
use Jtl\Connector\Core\Definition\Controller;
use Jtl\Connector\Core\Definition\Event;
use Jtl\Connector\Core\Event\CategoryEvent;
use Jtl\Connector\Core\Plugin\PluginInterface;
use Noodlehaus\ConfigInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;

//Every plugin which occurs in the connector/plugins is registered by the connector
class Bootstrap implements PluginInterface
{
    //Using the registerListener function, provided by the PluginInterface to define when the plugin should call what method
    public function registerListener(ConfigInterface $config, Container $container, EventDispatcher $dispatcher)
    {
        //Using static variables to define the wanted event name which is used to determine when the plugins is called
        $eventName = Event::createEventName(
            Controller::CATEGORY,
            Action::PUSH,
            Event::BEFORE
        );
        
        $dispatcher->addListener($eventName, [$this, "handle"]);
    }
    
    public function handle(CategoryEvent $event)
    {
        foreach ($event->getCategory()->getI18ns() as $i18n) {
            $i18n->setName(sprintf("%s_suffix", $i18n->getName()));
        }
    }
}
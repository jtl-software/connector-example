<?php

namespace DemoPlugin;

use DemoPlugin\GlobalDataListener;
use Jtl\Connector\Core\Definition\Action;
use Jtl\Connector\Core\Definition\Controller;
use Jtl\Connector\Core\Definition\Event;
use Jtl\Connector\Core\Plugin\PluginInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;

class bootstrap implements PluginInterface
{
    public function registerListener(EventDispatcher $dispatcher)
    {
        $eventName = Event::createEventName(
            Controller::GLOBAL_DATA,
            Action::PULL,
            Event::BEFORE
        );
        
        $dispatcher->addListener($eventName, [new GlobalDataListener, "handle"]);
    }
}
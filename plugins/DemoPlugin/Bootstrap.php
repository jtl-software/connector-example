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

class Bootstrap implements PluginInterface
{
    public function registerListener(ConfigInterface $config, Container $container, EventDispatcher $dispatcher)
    {
        $eventName = Event::createEventName(
            Controller::CATEGORY,
            Action::PUSH,
            Event::BEFORE
        );
        
        $dispatcher->addListener($eventName, [$this, "handle"]);
    }
    
    public function handle(CategoryEvent $event) {
        foreach ($event->getCategory()->getI18ns() as $i18n) {
            $i18n->setName(sprintf("%s_suffix", $i18n->getName()));
        }
    }
}
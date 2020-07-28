<?php

namespace DemoPlugin;

class GlobalDataListener
{
    public function handle($event, $eventName, $eventDispatcher) {
        file_put_contents(
            '../logs/log.json',
            sprintf('%s - %s', date('c'), $eventName),
            FILE_APPEND
        );
    }
}
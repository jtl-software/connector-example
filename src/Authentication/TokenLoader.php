<?php

namespace Jtl\Connector\Example\Authentication;

use jtl\Connector\Authentication\ITokenLoader;

class TokenLoader implements ITokenLoader
{

    /**
     * Loads the connector token
     *
     * @return string
     */
    public function load()
    {
        // Static example token
        // TODO: Replace by a more secure one
        return 'miesu5eicaech6ohy5aigh0aiz6toh7O';
    }
}

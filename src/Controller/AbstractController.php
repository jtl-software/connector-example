<?php

namespace Jtl\Connector\Example\Controller;

use PDO;

abstract class AbstractController
{
    protected $database;
    
    public function __construct(PDO $database)
    {
        $this->database = $database;
    }
}
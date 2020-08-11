<?php

namespace Jtl\Connector\Example\Controller;

use PDO;

//Creating an abstract controller class the pass the database object only once
abstract class AbstractController
{
    protected $database;
    
    //Using the DI container the access the previously defined database by demanding a PDO object in the class constructor
    public function __construct(PDO $database)
    {
        $this->database = $database;
    }
}
<?php

namespace Jtl\Connector\Example\Controller;

use PDO;

/**
 * Creating an abstract controller class to pass the database object only once
 *
 * Class AbstractController
 * @package Jtl\Connector\Example\Controller
 */
abstract class AbstractController
{
    /**
     * @var PDO
     */
    protected $pdo;

    /**
     * Using the DI container the access the previously defined database by demanding a PDO object in the class constructor
     *
     * AbstractController constructor.
     * @param PDO $pdo
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }
}

<?php

namespace Jtl\Connector\Example\Controller;

use PDO;

/**
 * Abstract controller class to pass the database object only once.
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
     * Using direct dependencies for better testing and easier use with a DI container.
     *
     * AbstractController constructor.
     * @param PDO $pdo
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }
}

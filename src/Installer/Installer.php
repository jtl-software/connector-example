<?php

namespace Jtl\Connector\Example\Installer;

use PDO;

class Installer
{
    /**
     * @var PDO
     */
    protected $pdo;

    /**
     * @var string
     */
    protected $connectorDir;

    /**
     * Installer constructor.
     * @param PDO $pdo
     * @param string $connectorDir
     */
    public function __construct(PDO $pdo, string $connectorDir)
    {
        $this->pdo = $pdo;
        $this->connectorDir = $connectorDir;
    }

    /**
     * Getting and executing all install scripts, to setup the needed connector mapping tables as well as demo shop tables.
     */
    public function run(): void
    {
        $scripts = glob(sprintf("%s/scripts/*.sql", $this->connectorDir));
        
        foreach ($scripts as $script) {
            $statement = $this->pdo->prepare(file_get_contents($script));
            $statement->execute();
        }
    }
}

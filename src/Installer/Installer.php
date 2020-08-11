<?php

namespace Jtl\Connector\Example\Installer;

use PDO;

class Installer
{
    protected $db;
    protected $config;
    
    public function __construct(PDO $database)
    {
        $this->db = $database;
    }
    
    public function run()
    {
        //Getting and executing all install scripts to setup the needed connector mapping tables as well as demo shop tables
        $scripts = glob(sprintf("%s/scripts/*.sql", CONNECTOR_DIR));
        
        foreach ($scripts as $script) {
            $statement = $this->db->prepare(file_get_contents($script));
            $statement->execute();
        }
    }
}
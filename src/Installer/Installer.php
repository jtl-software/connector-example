<?php

namespace Jtl\Connector\Example\Installer;

use Jtl\Connector\Core\Config\FileConfig;
use PDO;

class Installer
{
    protected $db;
    protected $config;
    
    public function __construct(FileConfig $config)
    {
        $dbParams = $config->get("db");
        $this->db = new PDO(
            sprintf("mysql:host=%s;dbname=%s", $dbParams["host"], $dbParams["name"]),
            $dbParams["username"],
            $dbParams["password"]
        );
    
        $this->config = $config;
    }
    
    public function run() {
        $scripts = glob(CONNECTOR_DIR . "/src/Installer/scripts/*");
    
        foreach ($scripts as $script) {
            $statement = $this->db->prepare(file_get_contents($script));
            $statement->execute();
        }
        
        if (!$this->config->has('token')){
            $this->config->set("token", "123456789");
            $this->config->write();
        }
    }
}
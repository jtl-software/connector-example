<?php

namespace Jtl\Connector\Example;

use Exception;
use Jtl\Connector\Core\Database\DatabaseInterface;
use Jtl\Connector\Core\Exception\DatabaseException;
use PDO;

class PDOInterface implements DatabaseInterface
{
    protected $host;
    protected $dbName;
    protected $username;
    protected $password;
    protected $isConnected;
    
    /**
     * @var PDO
     */
    protected $db;
    
    /**
     * @inheritDoc
     * @throws DatabaseException
     */
    public function connect(array $options = null)
    {
        $this->setOptions($options);
    
        try {
            $this->db = new PDO(
                sprintf("mysql:host=%s;dbname=%s", $this->host, $this->dbName),
                $this->username,
                $this->password,
                array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
            );
        }catch (Exception $e) {
            $this->isConnected = false;
            throw new DatabaseException($e->getMessage());
        }
        
        $this->isConnected = true;
    }
    
    /**
     * @inheritDoc
     */
    public function close()
    {
    }
    
    /**
     * @inheritDoc
     */
    public function query($query)
    {
        $result = $this->db->query($query);
        
        if ($result === false) {
            throw new DatabaseException("Could not send querry: " . $query);
        }
    }
    
    /**
     * @inheritDoc
     */
    public function isConnected()
    {
        return $this->isConnected;
    }
    
    /**
     * @inheritDoc
     */
    public function escapeString($query)
    {
        $this->db->quote($query);
    }
    
    private function setOptions(array $options) {
        if (isset($options["host"])) {
            $this->host = $options["host"];
        }
        
        if (isset($options["dbName"])) {
            $this->dbName = $options["dbName"];
        }
        
        if (isset($options["username"])) {
            $this->username = $options["username"];
        }
        
        if (isset($options["password"])) {
            $this->password = $options["password"];
        }
    }
}
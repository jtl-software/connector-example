<?php

namespace Jtl\Connector\Example\Mapper;

use Jtl\Connector\Core\Config\FileConfig;
use Jtl\Connector\Core\Mapper\PrimaryKeyMapperInterface;
use PDO;

class PrimaryKeyMapper implements PrimaryKeyMapperInterface
{
    protected $db;
    
    public function __construct(FileConfig $config)
    {
        $dbParams = $config->get("db");
        $this->db = new PDO(
            sprintf("mysql:host=%s;dbname=%s", $dbParams["host"], $dbParams["name"]),
            $dbParams["username"],
            $dbParams["password"],
            array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
        );
    }
    
    /**
     * @inheritDoc
     */
    public function getHostId(int $type, string $endpointId) : ?int
    {
        $statement = $this->db->prepare(sprintf('SELECT host FROM mapping WHERE endpoint = %s AND type = %s', $endpointId, $type));
        $statement->execute();
        
        return $statement->fetch();
    }
    
    /**
     * @inheritDoc
     */
    public function getEndpointId(int $type, int $hostId) : ?string
    {
        $statement = $this->db->prepare(sprintf('SELECT endpoint FROM mapping WHERE host = %s AND type = %s', $hostId, $type));
        $statement->execute();
    
        return $statement->fetch();
    }
    
    /**
     * @inheritDoc
     */
    public function save(int $type, string $endpointId, int $hostId) : bool
    {
        $statement = $this->db->prepare(sprintf('INSERT INTO mapping (endpoint, host, type) VALUES (%s, %s, %s)', $endpointId, $hostId, $type));
        $statement->execute();
    
        return $statement->fetch() !== false;
    }
    
    /**
     * @inheritDoc
     */
    public function delete(int $type, string $endpointId = null, int $hostId = null) : bool
    {
        $where = '';
        if ($endpointId !== null && $hostId !== null) {
            $where = sprintf('WHERE endpoint = %s AND host = %s AND type = %s', $endpointId, $hostId, $type);
        } elseif ($endpointId !== null) {
            $where = sprintf('WHERE endpoint = %s AND type = %s', $endpointId, $type);
        } elseif ($hostId !== null) {
            $where = sprintf('WHERE host = %s AND type = %s', $hostId, $type);
        }
    
        $statement = $this->db->prepare(sprintf('DELETE FROM mapping %s', $where));
        $statement->execute();
    
        return $statement->fetch();
    }
    
    /**
     * @inheritDoc
     */
    public function clear(int $type = null) : bool
    {
        $statement = $this->db->prepare('DELETE FROM mapping');
        $statement->execute();
    
        return $statement->fetch();
    }
}
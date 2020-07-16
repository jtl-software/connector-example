<?php

namespace Jtl\Connector\Example\Mapper;

use Jtl\Connector\Core\Mapper\PrimaryKeyMapperInterface;
use Jtl\Connector\Example\PDOInterface;

class PrimaryKeyMapper implements PrimaryKeyMapperInterface
{
    protected $db;
    
    public function __construct()
    {
        $pdo = new PDOInterface();
        if (!$pdo->isConnected()) {
            $pdo->connect([
                "host" => "localhost",
                "dbName" => "example_connector_db",
                "username" => "root",
                "password" => "jtlgmbh",
            ]);
        }
    
        $this->db = $pdo;
    }
    
    /**
     * @inheritDoc
     */
    public function getHostId(int $type, string $endpointId) : ?int
    {
        return $this->db->fetchSingle(sprintf('SELECT host FROM mapping WHERE endpoint = %s AND type = %s', $endpointId, $type));
    }
    
    /**
     * @inheritDoc
     */
    public function getEndpointId(int $type, int $hostId) : ?string
    {
        return $this->db->fetchSingle(sprintf('SELECT endpoint FROM mapping WHERE host = %s AND type = %s', $hostId, $type));
    }
    
    /**
     * @inheritDoc
     */
    public function save(int $type, string $endpointId, int $hostId) : bool
    {
        $id = $this->db->insert(sprintf('INSERT INTO mapping (endpoint, host, type) VALUES (%s, %s, %s)', $endpointId, $hostId, $type));
    
        return $id !== false;
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
    
        return $this->db->query(sprintf('DELETE FROM mapping %s'), $where);
    }
    
    /**
     * @inheritDoc
     */
    public function clear(int $type = null) : bool
    {
        return $this->db->query('DELETE FROM mapping');
    }
}
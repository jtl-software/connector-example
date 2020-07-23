<?php
/**
 * @copyright 2010-2013 JTL-Software GmbH
 * @package Jtl\Connector\Example\Mapper
 */

namespace Jtl\Connector\Example\Mapper;

use jtl\Connector\Core\IO\Path;
use jtl\Connector\Database\Sqlite3;
use jtl\Connector\Mapper\IPrimaryKeyMapper;

class PrimaryKeyMapper implements IPrimaryKeyMapper
{
    protected $db;

    public function __construct()
    {
        $sqlite3 = Sqlite3::getInstance();
        if (!$sqlite3->isConnected()) {
            $sqlite3->connect(array('location' => Path::combine(CONNECTOR_DIR, 'db', 'connector.s3db')));
        }

        $this->db = $sqlite3;
    }

    /**
     * Host ID getter
     *
     * @param string $endpointId
     * @param integer $type
     * @return integer|null
     */
    public function getHostId($endpointId, $type)
    {
        return $this->db->fetchSingle(sprintf('SELECT host FROM mapping WHERE endpoint = %s AND type = %s', $endpointId, $type));
    }

    /**
     * Endpoint ID getter
     *
     * @param integer $hostId
     * @param integer $type
     * @param string $relationType
     * @return string|null
     */
    public function getEndpointId($hostId, $type, $relationType = null)
    {
        // @todo: type 16 (Image) switch via $relationType

        return $this->db->fetchSingle(sprintf('SELECT endpoint FROM mapping WHERE host = %s AND type = %s', $hostId, $type));
    }

    /**
     * Save link to database
     *
     * @param string $endpointId
     * @param integer $hostId
     * @param integer $type
     * @return boolean
     */
    public function save($endpointId, $hostId, $type)
    {
        $id = $this->db->insert(sprintf('INSERT INTO mapping (endpoint, host, type) VALUES (%s, %s, %s)', $endpointId, $hostId, $type));

        return $id !== false;
    }

    /**
     * Delete link from database
     *
     * @param string $endpointId
     * @param integer $hostId
     * @param integer $type
     * @return boolean
     */
    public function delete($endpointId = null, $hostId = null, $type)
    {
        $where = '';
        if ($endpointId !== null && $hostId !== null) {
            $where = sprintf('WHERE endpoint = %s AND host = %s AND type = %s', $endpointId, $hostId, $type);
        } elseif ($endpointId !== null) {
            $where = sprintf('WHERE endpoint = %s AND type = %s', $endpointId, $type);
        } elseif ($hostId !== null) {
            $where = sprintf('WHERE host = %s AND type = %s', $hostId, $type);
        }

        return $this->db->query(sprintf('DELETE FROM mapping %s', $where));
    }

    /**
     * Clears the entire link table
     *
     * @return boolean
     */
    public function clear()
    {
        return $this->db->query('DELETE FROM mapping');
    }

    /**
     * Garbage Collect the entire link table
     *
     * @return boolean
     */
    public function gc()
    {
        return true;
    }
}

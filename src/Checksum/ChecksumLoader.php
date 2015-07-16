<?php
/**
 * @copyright 2010-2013 JTL-Software GmbH
 * @package jtl\Connector\Example\Checksum
 */

namespace jtl\Connector\Example\Checksum;

use jtl\Connector\Checksum\IChecksumLoader;
use jtl\Connector\Core\IO\Path;
use jtl\Connector\Database\Sqlite3;

class ChecksumLoader implements IChecksumLoader
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
     * Loads the checksum
     *
     * @param string $endpointId
     * @param int $type
     * @return string
     */
    public function read($endpointId, $type)
    {
        return $this->db->fetchSingle(sprintf('SELECT checksum FROM checksum WHERE endpoint = %s AND type = %s', $endpointId, $type));
    }

    /**
     * Loads the checksum
     *
     * @param string $endpointId
     * @param int $type
     * @param string $checksum
     * @return boolean
     */
    public function write($endpointId, $type, $checksum)
    {
        $id = $this->db->insert(sprintf('INSERT INTO checksum (endpoint, type, checksum) VALUES (%s, %s, %s)', $endpointId, $type, $checksum));

        return $id !== false;
    }

    /**
     * Loads the checksum
     *
     * @param string $endpointId
     * @param int $type
     * @return boolean
     */
    public function delete($endpointId, $type)
    {
        return $this->db->query(sprintf('DELETE FROM checksum WHERE endpoint = %s AND type = %s', $endpointId, $type));
    }
}

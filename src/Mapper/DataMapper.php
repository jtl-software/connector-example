<?php
/**
 * @copyright 2010-2013 JTL-Software GmbH
 * @package jtl\Connector\Example\Mapper
 */

namespace jtl\Connector\Example\Mapper;

use jtl\Connector\Core\IO\Path;
use jtl\Connector\Core\Utilities\ClassName;
use jtl\Connector\Core\Utilities\Singleton;
use jtl\Connector\Database\Sqlite3;
use jtl\Connector\Model\DataModel;

abstract class DataMapper extends Singleton
{
    protected $db;

    protected function __construct()
    {
        $sqlite3 = Sqlite3::getInstance();
        $sqlite3->connect(array('location' => Path::combine(CONNECTOR_DIR, 'db', 'connector.s3db')));

        $this->db = $sqlite3;
    }

    public function find($id)
    {
        $id = (int) $id;
        if ($id > 0) {
            $type = strtolower(ClassName::getFromNS(get_called_class()));
            return $this->db->fetchSingle(sprintf('SELECT data FROM %s WHERE id = %s', $type, $id));
        }

        return null;
    }

    public function findAll($limit = 100)
    {
        $result = [];
        $type = strtolower(ClassName::getFromNS(get_called_class()));
        $rows = $this->db->query(sprintf('SELECT data
                                  FROM %s t
                                  LEFT JOIN mapping m ON t.id = m.endpoint
                                  WHERE m.host IS NULL
                                  LIMIT %s', $type, $limit));

        if ($rows !== null) {
            foreach ($rows as $row) {
                $result[] = $row['data'];
            }
        }

        return $result;
    }

    public function save(DataModel &$model)
    {
        $json = $model->toJson();
        $type = strtolower(ClassName::getFromNS(get_called_class()));

        if ($model->getId()->getEndpoint() > 0) {
            $stmt = $this->db->prepare(sprintf('UPDATE %s SET data = :data WHERE id = :id', $type));
            $stmt->bindValue(':table', $type, SQLITE3_TEXT);
            $stmt->bindValue(':data', $json, SQLITE3_TEXT);
            $stmt->bindValue(':id', $model->getId()->getEndpoint(), SQLITE3_INTEGER);

            $stmt->execute();
        } else {
            $stmt = $this->db->prepare(sprintf('INSERT INTO %s (id, data) VALUES (null, :data)', $type));
            $stmt->bindValue(':table', $type, SQLITE3_TEXT);
            $stmt->bindValue(':data', $json, SQLITE3_TEXT);

            $result = $stmt->execute();
            if ($result) {
                $model->getId()->setEndpoint($this->db->getLastInsertRowId());
            }
        }
    }

    public function remove($id)
    {
        $id = (int) $id;
        if ($id > 0) {
            $type = strtolower(ClassName::getFromNS(get_called_class()));

            return $this->db->query(sprintf('DELETE FROM %s WHERE id = %s', $type, $id));
        }

        return false;
    }

    public function fetchCount()
    {
        $type = strtolower(ClassName::getFromNS(get_called_class()));
        return (int) $this->db->fetchSingle(sprintf('SELECT count(*)
                                                      FROM %s t
                                                      LEFT JOIN mapping m ON t.id = m.endpoint
                                                      WHERE m.host IS NULL', $type));
    }
}
<?php

namespace Jtl\Connector\Example\Mapper;

use Jtl\Connector\Core\Mapper\PrimaryKeyMapperInterface;
use PDO;

class PrimaryKeyMapper implements PrimaryKeyMapperInterface
{
    /**
     * @var PDO
     */
    protected $pdo;

    /**
     * PrimaryKeyMapper constructor.
     * @param PDO $pdo
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Returns the corresponding hostId to a endpointId and type
     * @inheritDoc
     */
    public function getHostId(int $type, string $endpointId): ?int
    {
        $statement = $this->pdo->prepare('SELECT host FROM mappings WHERE endpoint = ? AND type = ?');
        $statement->execute([$endpointId, $type]);

        return $statement->fetch()['host'];
    }

    /**
     * Returns the corresponding endpointId to a hostId and type
     * @inheritDoc
     */
    public function getEndpointId(int $type, int $hostId): ?string
    {
        $statement = $this->pdo->prepare('SELECT endpoint FROM mappings WHERE host = ? AND type = ?');
        $statement->execute([$hostId, $type]);

        return $statement->fetch()['endpoint'];
    }

    /**
     * Saves one specific linking
     * @inheritDoc
     */
    public function save(int $type, string $endpointId, int $hostId): bool
    {
        $statement = $this->pdo->prepare('INSERT INTO mappings (endpoint, host, type) VALUES (?, ?, ?)');
        return $statement->execute([$endpointId, $hostId, $type]);
    }

    /**
     * Deletes a specific linking
     * @inheritDoc
     */
    public function delete(int $type, string $endpointId = null, int $hostId = null): bool
    {
        $where = [
            'type = ?',
        ];

        $params = [
            $type
        ];

        if ($endpointId !== null) {
            $where[] = 'endpoint = ?';
            $params[] = $endpointId;
        }

        if ($hostId !== null) {
            $where[] = 'host = ?';
            $params[] = $hostId;
        }

        $statement = $this->pdo->prepare(sprintf('DELETE IGNORE FROM mappings WHERE %s', join(' AND ', $where)));

        return $statement->execute($params);
    }

    /**
     * Clears either the whole mapping table or all entries of a certain type
     * @inheritDoc
     */
    public function clear(int $type = null): bool
    {
        if(!is_null($type)) {
            return $this->delete($type);
        }

        $statement = $this->pdo->prepare('DELETE FROM mappings');
        $statement->execute();

        return $statement->fetch();
    }
}

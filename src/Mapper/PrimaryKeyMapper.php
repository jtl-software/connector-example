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
     * @inheritDoc
     */
    public function getHostId(int $type, string $endpointId): ?int
    {
        $statement = $this->pdo->prepare('SELECT host FROM mapping WHERE endpoint = ? AND type = ?');
        $statement->execute([$endpointId, $type]);

        return $statement->fetch();
    }

    /**
     * @inheritDoc
     */
    public function getEndpointId(int $type, int $hostId): ?string
    {
        $statement = $this->pdo->prepare('SELECT endpoint FROM mapping WHERE host = ? AND type = ?');
        $statement->execute([$hostId, $type]);

        return $statement->fetch()['endpoint'];
    }

    /**
     * @inheritDoc
     */
    public function save(int $type, string $endpointId, int $hostId): bool
    {
        $statement = $this->pdo->prepare('INSERT INTO mapping (endpoint, host, type) VALUES (?, ?, ?)');
        return $statement->execute([$endpointId, $hostId, $type]);
    }

    /**
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

        $statement = $this->pdo->prepare(sprintf('DELETE IGNORE FROM mapping WHERE %s', join(' AND ', $where)));

        return $statement->execute($params);
    }

    /**
     * @inheritDoc
     */
    public function clear(int $type = null): bool
    {
        $statement = $this->pdo->prepare('DELETE FROM mapping');
        $statement->execute();

        return $statement->fetch();
    }
}

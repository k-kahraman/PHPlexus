<?php

namespace PHPlexus\Core\Database;

use PDO;
use Exception;

class PdoConnection implements DatabaseConnectionInterface {
    private ?PDO $pdo = null;
    private string $dsn;
    private ?string $username;
    private ?string $password;
    private array $options;

    public function __construct(string $dsn, ?string $username = null, ?string $password = null, array $options = []) {
        $this->dsn = $dsn;
        $this->username = $username;
        $this->password = $password;
        $this->options = $options + [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
    }

    public function getConnection(): PDO {
        if ($this->pdo === null) {
            $this->pdo = new PDO($this->dsn, $this->username, $this->password, $this->options);
        }
        return $this->pdo;
    }

    public function query(string $sql, array $params = []): array {
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function execute(string $sql, array $params = []): int {
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount();
    }

    public function transaction(callable $callback): mixed {
        $pdo = $this->getConnection();
        $pdo->beginTransaction();
        try {
            $result = $callback($this);
            $pdo->commit();
            return $result;
        } catch (Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
    }
}

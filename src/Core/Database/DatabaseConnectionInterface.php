<?php

namespace PHPlexus\Core\Database;

use PDO;

interface DatabaseConnectionInterface {
    public function getConnection(): PDO;
    public function query(string $sql, array $params = []): array;
    public function execute(string $sql, array $params = []): int;
    public function transaction(callable $callback): mixed;
}

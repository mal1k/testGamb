<?php
declare(strict_types=1);

namespace classes;

use Exception;
use mysqli;

class Database {
    private mysqli $connection;

    public function __construct(Environment $env) {
        $host = $env->get('DB_HOST');
        $username = $env->get('DB_USERNAME');
        $password = $env->get('DB_PASSWORD');
        $dbName = $env->get('DB_NAME');

        $this->connection = new mysqli($host, $username, $password, $dbName);

        if ($this->connection->connect_error) {
            throw new Exception("Connection failed: " . $this->connection->connect_error);
        }
    }

    public function getConnection(): mysqli {
        return $this->connection;
    }

    public function close(): void {
        $this->connection->close();
    }
}
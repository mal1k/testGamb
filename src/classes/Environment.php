<?php
declare(strict_types=1);

namespace classes;

use Exception;

class Environment {
    private $envPath =  __DIR__  . '/../.env';
    private array $data = [];

    public function __construct()
    {
        $this->load();
    }

    private function load(): self {
        if (!file_exists($this->envPath)) {
            throw new Exception("File {$this->envPath} does not exist.");
        }

        $lines = file($this->envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            // Skip comments
            if (str_starts_with($line, '#')) {
                continue;
            }

            [$key, $value] = explode('=', $line, 2);
            $this->data[trim($key)] = trim($value);
        }

        return $this;
    }

    public function get(string $key, ?string $default = null): ?string {
        return $this->data[$key] ?? $default;
    }

    public function getAll(): array {
        return $this->data;
    }
}
<?php
declare(strict_types=1);

namespace classes;

use DateTime;
use Exception;
use mysqli;

class User {
    private float $initTime;
    private mysqli $connection;
    
    // user variables
    public ?int $userId;
    private float $balance;
    private string $lastVisitDate;
    private string $birthDate;

    public function __construct(?int $userId = null) {
        $this->initTime = microtime(true);
        $this->userId = $userId;

        // connect to db
        $env = new Environment();
        $db = new Database($env);
        $this->connection = $db->getConnection();

        if ( isset($this->userId) ) {
            $this->fetchFromDatabase();
        }
    }

    public function setBalance(float $newBalance): void {
        $this->balance = $newBalance;
    }

    public function getBalance(): float {
        return $this->balance;
    }

    public function setLastVisitDate(string $newVisitDate): void {
        $this->lastVisitDate = $newVisitDate;
    }

    public function getLastVisitDate(): string {
        return $this->lastVisitDate;
    }

    public function setBirthDate(string $newBirthdate): void {
        $this->birthDate = $newBirthdate;
    }

    public function getBirthDate(): string {
        return $this->birthDate;
    }

    public function getAge(): int {
        $birthDate = new DateTime($this->birthDate);
        $currentDate = new DateTime();
    
        $interval = $birthDate->diff($currentDate);
    
        return $interval->y; // return years
    }

    public function getClassWorkTime(): float {
        return round(microtime(true) - $this->initTime, 2);
    }

    public function save(): array {
        empty($this->userId) ? $this->create() : $this->update();

        return $this->getData();
    }

    private function create(): void {
        $query = "INSERT INTO users (balance, lastVisitDate, birthDate) VALUES (?, ?, ?)";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("dss", $this->balance, $this->lastVisitDate, $this->birthDate);
        $stmt->execute();
        $this->userId = $this->connection->insert_id;
    }
    
    private function update(): void {
        $query = "UPDATE users SET balance = ?, lastVisitDate = ?, birthDate = ? WHERE id = ?";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("dssi", $this->balance, $this->lastVisitDate, $this->birthDate, $this->userId);
        $stmt->execute();
    }

    public function delete(): void {
        if (empty($this->userId)) {
            throw new Exception("Cannot delete a user without a valid userId");
        }
    
        $query = "DELETE FROM users WHERE id = ?";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("i", $this->userId);
        $stmt->execute();
    }

    public function getData(): array {
        return [
            'userId' => $this->userId,
            'balance' => $this->getBalance(),
            'lastVisitDate' => $this->getLastVisitDate(),
            'birthDate' => $this->getBirthDate(),
            'age' => $this->getAge()
        ];
    }

    private function fetchFromDatabase(): void {
        if (is_null($this->userId)) {
            throw new Exception("Cannot fetch data without a valid userId");
        }
    
        $query = "SELECT balance, lastVisitDate, birthDate FROM users WHERE id = ?";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("i", $this->userId);
        $stmt->execute();
    
        $result = $stmt->get_result();

        if ($result->num_rows <= 0) {
            throw new Exception("User with ID {$this->userId} not found");
        }

        $data = $result->fetch_assoc();

        $this->balance = $data['balance'];
        $this->lastVisitDate = $data['lastVisitDate'];
        $this->birthDate = $data['birthDate'];
    }
}
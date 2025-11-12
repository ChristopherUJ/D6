<?php

namespace App\Repositories;

use App\Models\Client;
use PDO;

class ClientRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function exists(int $id): bool
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM clients WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetchColumn() > 0;
    }

    public function findAll(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM clients ORDER BY name ASC");
        $clients = [];

        while ($data = $stmt->fetch()) {
            $clients[] = $this->hydrate($data);
        }

        return $clients;
    }

    private function hydrate(array $data): Client
    {
        return new Client(
            name: $data['name'],
            email: $data['email'],
            phone: $data['phone'],
            address: $data['address'],
            id: (int)$data['id'],
            createdAt: $data['created_at']
        );
    }
}

<?php

namespace App\Models;

class Client
{
    private ?int $id;
    private string $name;
    private ?string $email;
    private ?string $phone;
    private ?string $address;
    private ?string $createdAt;

    public function __construct(
        string $name,
        ?string $email = null,
        ?string $phone = null,
        ?string $address = null,
        ?int $id = null,
        ?string $createdAt = null
    ) {
        $this->name = $name;
        $this->email = $email;
        $this->phone = $phone;
        $this->address = $address;
        $this->id = $id;
        $this->createdAt = $createdAt;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function getCreatedAt(): ?string
    {
        return $this->createdAt;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'created_at' => $this->createdAt
        ];
    }
}

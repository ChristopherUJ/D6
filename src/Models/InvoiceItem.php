<?php

namespace App\Models;

class InvoiceItem
{
    private ?int $id;
    private ?int $invoiceId;
    private string $description;
    private float $amount;
    private bool $isTaxed;
    private ?string $createdAt;

    public function __construct(
        string $description,
        float $amount,
        bool $isTaxed = false,
        ?int $invoiceId = null,
        ?int $id = null,
        ?string $createdAt = null
    ) {
        $this->description = $description;
        $this->amount = $amount;
        $this->isTaxed = $isTaxed;
        $this->invoiceId = $invoiceId;
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

    public function getInvoiceId(): ?int
    {
        return $this->invoiceId;
    }

    public function setInvoiceId(int $invoiceId): void
    {
        $this->invoiceId = $invoiceId;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function isTaxed(): bool
    {
        return $this->isTaxed;
    }

    public function getCreatedAt(): ?string
    {
        return $this->createdAt;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'invoice_id' => $this->invoiceId,
            'description' => $this->description,
            'amount' => $this->amount,
            'is_taxed' => $this->isTaxed,
            'created_at' => $this->createdAt
        ];
    }
}

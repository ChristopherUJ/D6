<?php

namespace App\Models;

class Invoice
{
    private ?int $id;
    private string $invoiceNumber;
    private string $invoiceDate;
    private ?string $dueDate;
    private int $clientId;
    private float $subtotal;
    private float $taxRate;
    private float $taxAmount;
    private float $total;
    private ?string $comments;
    private ?string $createdAt;
    private ?string $updatedAt;
    /** @var InvoiceItem[] */
    private array $items = [];

    public function __construct(
        string $invoiceNumber,
        string $invoiceDate,
        int $clientId,
        float $subtotal = 0.00,
        float $taxRate = 0.00,
        float $taxAmount = 0.00,
        float $total = 0.00,
        ?string $dueDate = null,
        ?string $comments = null,
        ?int $id = null,
        ?string $createdAt = null,
        ?string $updatedAt = null
    ) {
        $this->invoiceNumber = $invoiceNumber;
        $this->invoiceDate = $invoiceDate;
        $this->clientId = $clientId;
        $this->subtotal = $subtotal;
        $this->taxRate = $taxRate;
        $this->taxAmount = $taxAmount;
        $this->total = $total;
        $this->dueDate = $dueDate;
        $this->comments = $comments;
        $this->id = $id;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getInvoiceNumber(): string
    {
        return $this->invoiceNumber;
    }

    public function getInvoiceDate(): string
    {
        return $this->invoiceDate;
    }

    public function getDueDate(): ?string
    {
        return $this->dueDate;
    }

    public function getClientId(): int
    {
        return $this->clientId;
    }

    public function getSubtotal(): float
    {
        return $this->subtotal;
    }

    public function setSubtotal(float $subtotal): void
    {
        $this->subtotal = $subtotal;
    }

    public function getTaxRate(): float
    {
        return $this->taxRate;
    }

    public function getTaxAmount(): float
    {
        return $this->taxAmount;
    }

    public function setTaxAmount(float $taxAmount): void
    {
        $this->taxAmount = $taxAmount;
    }

    public function getTotal(): float
    {
        return $this->total;
    }

    public function setTotal(float $total): void
    {
        $this->total = $total;
    }

    public function getComments(): ?string
    {
        return $this->comments;
    }

    public function getCreatedAt(): ?string
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?string
    {
        return $this->updatedAt;
    }

    /**
     * @return InvoiceItem[]
     */
    public function getItems(): array
    {
        return $this->items;
    }

    public function addItem(InvoiceItem $item): void
    {
        $this->items[] = $item;
    }

    /**
     * @param InvoiceItem[] $items
     */
    public function setItems(array $items): void
    {
        $this->items = $items;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'invoice_number' => $this->invoiceNumber,
            'invoice_date' => $this->invoiceDate,
            'due_date' => $this->dueDate,
            'client_id' => $this->clientId,
            'subtotal' => $this->subtotal,
            'tax_rate' => $this->taxRate,
            'tax_amount' => $this->taxAmount,
            'total' => $this->total,
            'comments' => $this->comments,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
            'items' => array_map(fn($item) => $item->toArray(), $this->items)
        ];
    }
}

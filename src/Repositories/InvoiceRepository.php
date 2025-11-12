<?php

namespace App\Repositories;

use App\Models\Invoice;
use PDO;

class InvoiceRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function save(Invoice $invoice): Invoice
    {
        $sql = "INSERT INTO invoices 
                (invoice_number, invoice_date, due_date, client_id, subtotal, tax_rate, tax_amount, total, comments) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            $invoice->getInvoiceNumber(),
            $invoice->getInvoiceDate(),
            $invoice->getDueDate(),
            $invoice->getClientId(),
            $invoice->getSubtotal(),
            $invoice->getTaxRate(),
            $invoice->getTaxAmount(),
            $invoice->getTotal(),
            $invoice->getComments()
        ]);

        $invoice->setId((int)$this->pdo->lastInsertId());
        return $invoice;
    }

    public function findById(int $id): ?Invoice
    {
        $stmt = $this->pdo->prepare("SELECT * FROM invoices WHERE id = ?");
        $stmt->execute([$id]);
        $data = $stmt->fetch();

        if (!$data) {
            return null;
        }

        return $this->hydrate($data);
    }

    public function getLastInvoiceNumber(): ?string
    {
        $stmt = $this->pdo->query(
            "SELECT invoice_number FROM invoices ORDER BY id DESC LIMIT 1"
        );
        return $stmt->fetchColumn() ?: null;
    }


    private function hydrate(array $data): Invoice
    {
        return new Invoice(
            invoiceNumber: $data['invoice_number'],
            invoiceDate: $data['invoice_date'],
            clientId: (int)$data['client_id'],
            subtotal: (float)$data['subtotal'],
            taxRate: (float)$data['tax_rate'],
            taxAmount: (float)$data['tax_amount'],
            total: (float)$data['total'],
            dueDate: $data['due_date'],
            comments: $data['comments'],
            id: (int)$data['id'],
            createdAt: $data['created_at'],
            updatedAt: $data['updated_at']
        );
    }
}

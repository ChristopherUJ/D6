<?php

namespace App\Repositories;

use App\Models\InvoiceItem;
use PDO;

class InvoiceItemRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function save(InvoiceItem $item): InvoiceItem
    {
        $sql = "INSERT INTO invoice_items (invoice_id, description, amount, is_taxed) VALUES (?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            $item->getInvoiceId(),
            $item->getDescription(),
            $item->getAmount(),
            $item->isTaxed() ? 1 : 0
        ]);

        $item->setId((int)$this->pdo->lastInsertId());
        return $item;
    }

    /**
     * @param InvoiceItem[] $items
     */
    public function saveMultiple(array $items): void
    {
        foreach ($items as $item) {
            $this->save($item);
        }
    }

    /**
     * @return InvoiceItem[]
     */
    public function findByInvoiceId(int $invoiceId): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM invoice_items WHERE invoice_id = ?");
        $stmt->execute([$invoiceId]);
        $results = $stmt->fetchAll();

        $items = [];
        foreach ($results as $data) {
            $items[] = $this->hydrate($data);
        }

        return $items;
    }

    private function hydrate(array $data): InvoiceItem
    {
        return new InvoiceItem(
            description: $data['description'],
            amount: (float)$data['amount'],
            isTaxed: (bool)$data['is_taxed'],
            invoiceId: (int)$data['invoice_id'],
            id: (int)$data['id'],
            createdAt: $data['created_at']
        );
    }
}

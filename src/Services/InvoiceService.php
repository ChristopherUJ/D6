<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Repositories\ClientRepository;
use App\Repositories\InvoiceItemRepository;
use App\Repositories\InvoiceRepository;
use Exception;
use PDO;

class InvoiceService
{
    private PDO $pdo;
    private InvoiceRepository $invoiceRepository;
    private InvoiceItemRepository $itemRepository;
    private ClientRepository $clientRepository;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->invoiceRepository = new InvoiceRepository($pdo);
        $this->itemRepository = new InvoiceItemRepository($pdo);
        $this->clientRepository = new ClientRepository($pdo);
    }

    /**
     * Create a new invoice with items
     *
     * @param array $data
     * @return Invoice
     * @throws Exception
     */
    public function createInvoice(array $data): Invoice
    {
        // Validate required fields
        $this->validateInvoiceData($data);

        // Validate client exists
        if (!$this->clientRepository->exists($data['client_id'])) {
            throw new Exception("Invalid Client ID");
        }

        // Generate invoice number if not provided
        $invoiceNumber = $this->generateInvoiceNumber();

        // Process line items
        $items = $this->processLineItems($data);

        if (empty($items)) {
            throw new Exception("At least one valid invoice item is required");
        }

        // Calculate totals
        $calculations = $this->calculateTotals($items, $data['tax_rate'] ?? 0);

        // Create invoice object
        $invoice = new Invoice(
            invoiceNumber: $invoiceNumber,
            invoiceDate: $data['invoice_date'],
            clientId: (int)$data['client_id'],
            subtotal: $calculations['subtotal'],
            taxRate: $calculations['tax_rate'],
            taxAmount: $calculations['tax_amount'],
            total: $calculations['total'],
            dueDate: $data['due_date'] ?? null,
            comments: !empty($data['comments']) ? trim($data['comments']) : null
        );

        // Set items
        $invoice->setItems($items);

        // Save invoice and items in transaction
        $this->pdo->beginTransaction();
        try {
            $invoice = $this->invoiceRepository->save($invoice);

            // Set invoice_id for each item and save
            foreach ($items as $item) {
                $item->setInvoiceId($invoice->getId());
            }
            $this->itemRepository->saveMultiple($items);

            $this->pdo->commit();
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }

        return $invoice;
    }

    /**
     * Get invoice by ID with items
     */
    public function getInvoiceById(int $id): ?Invoice
    {
        $invoice = $this->invoiceRepository->findById($id);

        if ($invoice) {
            $items = $this->itemRepository->findByInvoiceId($id);
            $invoice->setItems($items);
        }

        return $invoice;
    }

    /**
     * Validate invoice data
     *
     * @throws Exception
     */
    private function validateInvoiceData(array $data): void
    {
        $requiredFields = ['invoice_date', 'client_id'];
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                throw new Exception("Missing required field: $field");
            }
        }

        // Validate invoice date format
        if (!\DateTime::createFromFormat('Y-m-d', $data['invoice_date'])) {
            throw new Exception("Invalid invoice date format");
        }

        // Validate due date format if provided
        if (!empty($data['due_date']) && !\DateTime::createFromFormat('Y-m-d', $data['due_date'])) {
            throw new Exception("Invalid due date format");
        }
    }

    /**
     * Process and validate line items from request data
     *
     * @return InvoiceItem[]
     * @throws Exception
     */
    private function processLineItems(array $data): array
    {
        $descriptions = $data['description'] ?? [];
        $amounts = $data['amount'] ?? [];
        $taxedItems = $data['taxed'] ?? [];

        if (empty($descriptions) || empty($amounts)) {
            throw new Exception("At least one invoice item is required");
        }

        $items = [];
        foreach ($descriptions as $index => $description) {
            if (empty(trim($description)) || !isset($amounts[$index])) {
                continue;
            }

            $amount = (float)$amounts[$index];
            $isTaxed = isset($taxedItems[$index]);

            if ($amount <= 0) {
                throw new Exception("Item amount must be greater than zero");
            }

            $items[] = new InvoiceItem(
                description: trim($description),
                amount: $amount,
                isTaxed: $isTaxed
            );
        }

        return $items;
    }

    /**
     * Calculate invoice totals
     *
     * @param InvoiceItem[] $items
     * @param float $taxRate
     * @return array
     */
    private function calculateTotals(array $items, float $taxRate): array
    {
        $subtotal = 0;
        $taxableAmount = 0;

        foreach ($items as $item) {
            $subtotal += $item->getAmount();
            if ($item->isTaxed()) {
                $taxableAmount += $item->getAmount();
            }
        }

        $taxAmount = $taxableAmount * ($taxRate / 100);
        $total = $subtotal + $taxAmount;

        return [
            'subtotal' => $subtotal,
            'tax_rate' => $taxRate,
            'tax_amount' => $taxAmount,
            'total' => $total
        ];
    }

    /**
     * Generate unique invoice number
     */
    private function generateInvoiceNumber(): string
    {
        $lastInvoice = $this->invoiceRepository->getLastInvoiceNumber();

        if ($lastInvoice) {
            $lastNumber = (int)str_replace('INV-', '', $lastInvoice);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return 'INV-' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

}

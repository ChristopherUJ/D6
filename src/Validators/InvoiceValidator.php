<?php

namespace App\Validators;

use DateTime;
use Exception;

class InvoiceValidator
{
    /**
     * Validate invoice data
     * @throws Exception
     */
    public function validate(array $data): void
    {
        // Check required fields
        $requiredFields = ['invoice_date', 'due_date', 'client_id'];
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                throw new Exception("Missing required field: {$field}");
            }
        }

        // Validate dates
        if (!DateTime::createFromFormat('Y-m-d', $data['invoice_date'])) {
            throw new Exception("Invalid invoice date format. Expected Y-m-d");
        }
        if (!DateTime::createFromFormat('Y-m-d', $data['due_date'])) {
            throw new Exception("Invalid due date format. Expected Y-m-d");
        }

        // Validate client_id is numeric - might be worth checking if client exists
        if (!is_numeric($data['client_id'])) {
            throw new Exception("Client ID must be numeric");
        }

        // Validate tax_rate
        if (isset($data['tax_rate'])) {
            $taxRate = (float)$data['tax_rate'];
            if ($taxRate < 0 || $taxRate > 100) {
                throw new Exception("Tax rate must be between 0 and 100");
            }
        }

        // Validate invoice items
        $this->validateInvoiceItems($data);
    }

    /**
     * Validate that at least one valid invoice item exists
     * @throws Exception
     */
    private function validateInvoiceItems(array $data): void
    {
        $descriptions = $data['description'] ?? [];
        $amounts = $data['amount'] ?? [];

        if (empty($descriptions) || empty($amounts)) {
            throw new Exception("At least one invoice item is required");
        }

        // Check if there's at least one valid line item
        $hasValidItem = false;
        foreach ($descriptions as $index => $description) {
            if (!empty(trim($description)) && isset($amounts[$index])) {
                $amount = (float)$amounts[$index];
                if ($amount > 0) {
                    $hasValidItem = true;
                    break;
                }
            }
        }

        if (!$hasValidItem) {
            throw new Exception("At least one valid invoice item with description and amount greater than zero is required");
        }
    }

}

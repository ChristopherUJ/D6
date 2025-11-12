<?php

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../autoload.php';

use App\Services\InvoiceService;
use App\Repositories\InvoiceRepository;
use App\Repositories\InvoiceItemRepository;
use App\Repositories\ClientRepository;
use App\Validators\InvoiceValidator;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

try {
    // Initialise db connection
    $database = new Database();
    $pdo = $database->getConnection();

    // Initialise dependencies
    $itemRepository = new InvoiceItemRepository($pdo);
    $invoiceRepository = new InvoiceRepository($pdo);
    $clientRepository = new ClientRepository($pdo);
    $validator = new InvoiceValidator();

    $invoiceService = new InvoiceService($pdo);

    // Validate the request data
    $validator->validate($_POST);

    // Create a new invoice
    $invoice = $invoiceService->createInvoice($_POST);

    // Return success response
    http_response_code(201);
    echo json_encode([
        'success' => true,
        'message' => 'Invoice saved successfully',
        'invoice_id' => $invoice->getId(),
        'invoice_number' => $invoice->getInvoiceNumber(),
        'total' => number_format($invoice->getTotal(), 2),
        'data' => $invoice->toArray()
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}

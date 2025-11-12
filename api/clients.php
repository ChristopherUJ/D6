<?php


header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../autoload.php';

use App\Repositories\ClientRepository;

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

try {
    // Initialise db connection
    $database = new Database();
    $pdo = $database->getConnection();

    // Initialise repository
    $clientRepository = new ClientRepository($pdo);

    // Fetch all clients
    $clients = $clientRepository->findAll();

    // Convert to array format
    $clientsArray = array_map(function ($client) {
        return [
            'id' => $client->getId(),
            'name' => $client->getName(),
            'email' => $client->getEmail(),
            'phone' => $client->getPhone(),
            'address' => $client->getAddress()
        ];
    }, $clients);

    // Return success response
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'data' => $clientsArray
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}

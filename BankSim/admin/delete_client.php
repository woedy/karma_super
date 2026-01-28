<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/Auth.php';
require_once __DIR__ . '/../includes/Database.php';
require_once __DIR__ . '/../includes/functions.php';

Auth::requireAdmin();

header('Content-Type: application/json');

if (!isPost()) {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$clientId = getPost('id');

if (empty($clientId)) {
    echo json_encode(['success' => false, 'message' => 'Client ID is required']);
    exit;
}

$db = Database::getInstance();

try {
    // Check if client exists
    $client = $db->queryOne("SELECT full_name FROM clients WHERE id = ?", [$clientId]);
    
    if (!$client) {
        echo json_encode(['success' => false, 'message' => 'Client not found']);
        exit;
    }
    
    // Delete client (cascades to accounts and transactions via FK constraints)
    $db->execute("DELETE FROM clients WHERE id = ?", [$clientId]);
    
    setFlashMessage('success', "Client '{$client['full_name']}' has been deleted successfully");
    echo json_encode(['success' => true]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error deleting client: ' . $e->getMessage()]);
}

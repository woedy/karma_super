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

$transactionId = getPost('id');

if (empty($transactionId)) {
    echo json_encode(['success' => false, 'message' => 'Transaction ID is required']);
    exit;
}

$db = Database::getInstance();

try {
    // Check if transaction exists and is PENDING
    $transaction = $db->queryOne("SELECT id, status, transaction_type FROM transactions WHERE id = ?", [$transactionId]);
    
    if (!$transaction) {
        echo json_encode(['success' => false, 'message' => 'Transaction not found']);
        exit;
    }
    
    if ($transaction['status'] !== 'PENDING') {
        echo json_encode(['success' => false, 'message' => 'Only PENDING transactions can be deleted']);
        exit;
    }
    
    // Delete transaction
    $db->execute("DELETE FROM transactions WHERE id = ?", [$transactionId]);
    
    setFlashMessage('success', "Transaction #{$transactionId} has been deleted successfully");
    echo json_encode(['success' => true]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error deleting transaction: ' . $e->getMessage()]);
}

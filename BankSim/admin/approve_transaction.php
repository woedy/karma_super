<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/Auth.php';
require_once __DIR__ . '/../includes/Database.php';
require_once __DIR__ . '/../includes/functions.php';

Auth::requireAdmin();

$db = Database::getInstance();

// Get parameters
$transactionId = getGet('id');
$action = getGet('action'); // 'approve' or 'reject'

if (!$transactionId || !in_array($action, ['approve', 'reject'])) {
    setFlashMessage('error', 'Invalid request');
    redirect('/BankSim/admin/pending_transactions.php');
}

// Get transaction details
$transaction = $db->queryOne("
    SELECT t.*, a.account_number, a.balance, a.client_id
    FROM transactions t
    JOIN accounts a ON t.account_id = a.id
    WHERE t.id = ? AND t.status = 'PENDING'
", [$transactionId]);

if (!$transaction) {
    setFlashMessage('error', 'Transaction not found or already processed');
    redirect('/BankSim/admin/pending_transactions.php');
}

// Start transaction
$db->beginTransaction();

try {
    $adminId = Auth::getAdminId();
    $newStatus = $action === 'approve' ? 'COMPLETED' : 'REJECTED';
    
    // Update transaction status
    $db->execute("
        UPDATE transactions 
        SET status = ?, processed_at = NOW(), processed_by = ?
        WHERE id = ?
    ", [$newStatus, $adminId, $transactionId]);
    
    // Record approval/rejection
    $db->execute("
        INSERT INTO transaction_approvals (transaction_id, admin_id, action, created_at)
        VALUES (?, ?, ?, NOW())
    ", [$transactionId, $adminId, strtoupper($action) . 'D']);
    
    // If approved, update account balances
    if ($action === 'approve') {
        $amount = $transaction['amount'];
        $accountId = $transaction['account_id'];
        
        switch ($transaction['transaction_type']) {
            case 'DEPOSIT':
                // Add to account balance
                $db->execute("UPDATE accounts SET balance = balance + ? WHERE id = ?", [$amount, $accountId]);
                break;
                
            case 'WITHDRAWAL':
                // Check sufficient balance
                if ($transaction['balance'] < $amount) {
                    throw new Exception('Insufficient balance');
                }
                // Deduct from account balance
                $db->execute("UPDATE accounts SET balance = balance - ? WHERE id = ?", [$amount, $accountId]);
                break;
                
            case 'INTERNAL_TRANSFER':
                // Check sufficient balance
                if ($transaction['balance'] < $amount) {
                    throw new Exception('Insufficient balance');
                }
                
                // Deduct from sender
                $db->execute("UPDATE accounts SET balance = balance - ? WHERE id = ?", [$amount, $accountId]);
                
                // Find recipient account
                $recipientAccount = $db->queryOne("SELECT id FROM accounts WHERE account_number = ?", [$transaction['recipient_account']]);
                
                if ($recipientAccount) {
                    // Add to recipient
                    $db->execute("UPDATE accounts SET balance = balance + ? WHERE id = ?", [$amount, $recipientAccount['id']]);
                    
                    // Create corresponding credit transaction for recipient
                    $db->execute("
                        INSERT INTO transactions (account_id, transaction_type, amount, description, status, created_at, processed_at, processed_by)
                        VALUES (?, 'DEPOSIT', ?, ?, 'COMPLETED', NOW(), NOW(), ?)
                    ", [
                        $recipientAccount['id'],
                        $amount,
                        'Internal transfer from ' . $transaction['account_number'],
                        $adminId
                    ]);
                }
                break;
                
            case 'EXTERNAL_TRANSFER':
                // Check sufficient balance
                if ($transaction['balance'] < $amount) {
                    throw new Exception('Insufficient balance');
                }
                // Deduct from account balance (external transfer just deducts)
                $db->execute("UPDATE accounts SET balance = balance - ? WHERE id = ?", [$amount, $accountId]);
                break;
        }
    }
    
    $db->commit();
    
    $actionText = $action === 'approve' ? 'approved' : 'rejected';
    setFlashMessage('success', "Transaction #{$transactionId} has been {$actionText} successfully");
    
} catch (Exception $e) {
    $db->rollback();
    setFlashMessage('error', 'Error processing transaction: ' . $e->getMessage());
}

redirect('/BankSim/admin/pending_transactions.php');

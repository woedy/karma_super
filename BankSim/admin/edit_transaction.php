<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/Auth.php';
require_once __DIR__ . '/../includes/Database.php';
require_once __DIR__ . '/../includes/functions.php';

Auth::requireAdmin();

$db = Database::getInstance();
$errors = [];
$transactionId = getGet('id');

if (empty($transactionId)) {
    setFlashMessage('error', 'Transaction ID is required');
    redirect('/BankSim/admin/transactions.php');
}

// Get pending transaction details
$transaction = $db->queryOne("
    SELECT t.*, c.full_name, a.account_number, a.balance as current_balance
    FROM transactions t
    JOIN accounts a ON t.account_id = a.id
    JOIN clients c ON a.client_id = c.id
    WHERE t.id = ?
", [$transactionId]);

if (!$transaction) {
    setFlashMessage('error', 'Transaction not found');
    redirect('/BankSim/admin/transactions.php');
}

if ($transaction['status'] !== 'PENDING') {
    setFlashMessage('error', 'Only PENDING transactions can be edited');
    redirect('/BankSim/admin/transactions.php');
}

if (isPost()) {
    $amount = getPost('amount');
    $description = trim(getPost('description'));
    $recipientName = trim(getPost('recipient_name'));
    $recipientAccount = trim(getPost('recipient_account'));
    $recipientBank = trim(getPost('recipient_bank'));
    
    // Validation
    if (empty($amount) || !is_numeric($amount) || $amount <= 0) {
        $errors[] = "Valid amount is required";
    }
    
    $type = $transaction['transaction_type'];
    
    // Additional validation for transfers
    if ($type === 'INTERNAL_TRANSFER' || $type === 'EXTERNAL_TRANSFER') {
        if (empty($recipientName)) $errors[] = "Recipient name is required";
        if (empty($recipientAccount)) $errors[] = "Recipient account is required";
        if ($type === 'EXTERNAL_TRANSFER' && empty($recipientBank)) $errors[] = "Recipient bank is required";
        
        // Check sufficient balance
        if ($amount > $transaction['current_balance']) {
            $errors[] = "Insufficient balance for this transaction";
        }
    } elseif ($type === 'WITHDRAWAL') {
        if ($amount > $transaction['current_balance']) {
            $errors[] = "Insufficient balance for withdrawal";
        }
    }
    
    if (empty($errors)) {
        try {
            $sql = "UPDATE transactions SET amount = ?, description = ?, 
                    recipient_name = ?, recipient_account = ?, recipient_bank = ? 
                    WHERE id = ? AND status = 'PENDING'";
            
            $params = [
                $amount,
                $description,
                $recipientName,
                $recipientAccount,
                $recipientBank,
                $transactionId
            ];
            
            $db->execute($sql, $params);
            
            setFlashMessage('success', "Transaction updated successfully");
            redirect('/BankSim/admin/transactions.php');
            
        } catch (Exception $e) {
            $errors[] = "Error updating transaction: " . $e->getMessage();
        }
    }
}

$pendingCount = $db->queryOne("SELECT COUNT(*) as count FROM transactions WHERE status = 'PENDING'")['count'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Transaction - <?php echo APP_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="container-fluid">
        <div class="row">
            <?php include 'includes/sidebar.php'; ?>
            
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Edit Transaction</h1>
                    <a href="/BankSim/admin/transactions.php" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Back to Transactions
                    </a>
                </div>
                
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>Please correct the following errors:</strong>
                        <ul class="mb-0 mt-2">
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo htmlspecialchars($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <div class="card">
                    <div class="card-body">
                        <form method="POST" action="" id="transactionForm">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Client Account</label>
                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($transaction['full_name']); ?> - <?php echo htmlspecialchars($transaction['account_number']); ?>" disabled>
                                    <small class="text-muted">Current Balance: <?php echo formatCurrency($transaction['current_balance']); ?></small>
                                    <input type="hidden" id="current_balance" value="<?php echo $transaction['current_balance']; ?>">
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Transaction Type</label>
                                    <input type="text" class="form-control" id="transaction_type_display" value="<?php echo htmlspecialchars($transaction['transaction_type']); ?>" disabled>
                                    <input type="hidden" id="transaction_type" value="<?php echo htmlspecialchars($transaction['transaction_type']); ?>">
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="amount" class="form-label">Amount <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><?php echo CURRENCY_SYMBOL; ?></span>
                                        <input type="number" step="0.01" min="0.01" class="form-control" id="amount" name="amount" 
                                               value="<?php echo htmlspecialchars(getPost('amount', $transaction['amount'])); ?>" required>
                                    </div>
                                    <small class="text-muted" id="balanceWarning" style="display:none; color: red !important;"></small>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <input type="text" class="form-control" id="description" name="description" 
                                           value="<?php echo htmlspecialchars(getPost('description', $transaction['description'])); ?>">
                                </div>
                            </div>
                            
                            <?php if (in_array($transaction['transaction_type'], ['INTERNAL_TRANSFER', 'EXTERNAL_TRANSFER'])): ?>
                            <div id="recipientFields">
                                <h5 class="mt-3 mb-3 border-bottom pb-2">Recipient Details</h5>
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="recipient_name" class="form-label">Recipient Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="recipient_name" name="recipient_name" 
                                               value="<?php echo htmlspecialchars(getPost('recipient_name', $transaction['recipient_name'])); ?>" required>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="recipient_account" class="form-label">Recipient Account No. <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="recipient_account" name="recipient_account" 
                                               value="<?php echo htmlspecialchars(getPost('recipient_account', $transaction['recipient_account'])); ?>" required>
                                    </div>
                                    <?php if ($transaction['transaction_type'] === 'EXTERNAL_TRANSFER'): ?>
                                    <div class="col-md-4 mb-3">
                                        <label for="recipient_bank" class="form-label">Recipient Bank <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="recipient_bank" name="recipient_bank" 
                                               value="<?php echo htmlspecialchars(getPost('recipient_bank', $transaction['recipient_bank'])); ?>" required>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php endif; ?>
                            
                            <div class="d-flex gap-2 mt-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-save"></i> Update Transaction
                                </button>
                                <a href="/BankSim/admin/transactions.php" class="btn btn-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Client-side balance check
        document.getElementById('amount').addEventListener('input', checkBalance);
        
        function checkBalance() {
            const amountInput = document.getElementById('amount');
            const currentBalance = parseFloat(document.getElementById('current_balance').value);
            const type = document.getElementById('transaction_type').value;
            const warningEl = document.getElementById('balanceWarning');
            
            if (amountInput.value) {
                const amount = parseFloat(amountInput.value);
                
                if ((type === 'WITHDRAWAL' || type.includes('TRANSFER')) && amount > currentBalance) {
                    warningEl.textContent = `Warning: Amount exceeds current balance ($${currentBalance.toFixed(2)})`;
                    warningEl.style.display = 'block';
                } else {
                    warningEl.style.display = 'none';
                }
            } else {
                warningEl.style.display = 'none';
            }
        }
    </script>
</body>
</html>

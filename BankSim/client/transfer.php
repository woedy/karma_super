<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/Auth.php';
require_once __DIR__ . '/../includes/Database.php';
require_once __DIR__ . '/../includes/functions.php';

Auth::requireClient();

$db = Database::getInstance();
$accountId = Auth::getAccountId();

// Get current balance, account number and status
$account = $db->queryOne("SELECT balance, account_number, status FROM accounts WHERE id = ?", [$accountId]);

if ($account['status'] === 'SUSPENDED') {
    setFlashMessage('error', 'Your account has been restricted. Please contact support at support@banksim.com or call 1-800-BANKSIM to resolve this situation.');
    redirect('/BankSim/client/dashboard.php');
}

// Handle form submission
if (isPost()) {
    $transferType = getPost('transfer_type'); // 'internal' or 'external'
    $amount = getPost('amount');
    $recipientAccount = sanitizeInput(getPost('recipient_account'));
    $recipientName = sanitizeInput(getPost('recipient_name'));
    $recipientBank = sanitizeInput(getPost('recipient_bank', ''));
    $description = sanitizeInput(getPost('description'));
    
    if (!validateAmount($amount)) {
        setFlashMessage('error', 'Please enter a valid amount');
    } elseif ($amount > $account['balance']) {
        setFlashMessage('error', 'Insufficient balance');
    } elseif (empty($recipientAccount) || empty($recipientName)) {
        setFlashMessage('error', 'Please fill in all required fields');
    } else {
        $transactionType = $transferType === 'internal' ? 'INTERNAL_TRANSFER' : 'EXTERNAL_TRANSFER';
        
        // For internal transfers, verify recipient exists
        if ($transferType === 'internal') {
            $recipientExists = $db->queryOne("SELECT id FROM accounts WHERE account_number = ?", [$recipientAccount]);
            if (!$recipientExists) {
                setFlashMessage('error', 'Recipient account not found');
            } elseif ($recipientAccount === $account['account_number']) {
                setFlashMessage('error', 'Cannot transfer to your own account');
            } else {
                // Create transfer transaction
                $result = $db->execute("
                    INSERT INTO transactions (account_id, transaction_type, amount, recipient_account, recipient_name, description, status, created_at)
                    VALUES (?, ?, ?, ?, ?, ?, 'PENDING', NOW())
                ", [$accountId, $transactionType, $amount, $recipientAccount, $recipientName, $description]);
                
                if ($result) {
                    $transactionId = $db->lastInsertId();
                    setFlashMessage('success', "Transfer request submitted successfully! Reference: #$transactionId");
                    redirect('/BankSim/client/dashboard.php');
                } else {
                    setFlashMessage('error', 'Failed to submit transfer request');
                }
            }
        } else {
            // External transfer - no validation needed
            $result = $db->execute("
                INSERT INTO transactions (account_id, transaction_type, amount, recipient_account, recipient_name, recipient_bank, description, status, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, 'PENDING', NOW())
            ", [$accountId, $transactionType, $amount, $recipientAccount, $recipientName, $recipientBank, $description]);
            
            if ($result) {
                $transactionId = $db->lastInsertId();
                setFlashMessage('success', "Transfer request submitted successfully! Reference: #$transactionId");
                redirect('/BankSim/client/dashboard.php');
            } else {
                setFlashMessage('error', 'Failed to submit transfer request');
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transfer - <?php echo APP_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/client.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="bi bi-arrow-left-right"></i> Transfer Money</h5>
                    </div>
                    <div class="card-body">
                        <?php displayFlashMessage(); ?>
                        
                        <div class="alert alert-secondary">
                            <strong>Available Balance:</strong> <?php echo formatCurrency($account['balance']); ?>
                        </div>
                        
                        <!-- Transfer Type Tabs -->
                        <ul class="nav nav-tabs mb-3" id="transferTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="internal-tab" data-bs-toggle="tab" 
                                        data-bs-target="#internal" type="button" role="tab">
                                    <i class="bi bi-building"></i> Internal Transfer
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="external-tab" data-bs-toggle="tab" 
                                        data-bs-target="#external" type="button" role="tab">
                                    <i class="bi bi-globe"></i> External Transfer
                                </button>
                            </li>
                        </ul>
                        
                        <div class="tab-content" id="transferTabContent">
                            <!-- Internal Transfer -->
                            <div class="tab-pane fade show active" id="internal" role="tabpanel">
                                <form method="POST" action="">
                                    <input type="hidden" name="transfer_type" value="internal">
                                    
                                    <div class="mb-3">
                                        <label for="recipient_account_internal" class="form-label">Recipient Account Number</label>
                                        <input type="text" class="form-control" id="recipient_account_internal" 
                                               name="recipient_account" required>
                                        <small class="text-muted">Enter the account number within our bank</small>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="recipient_name_internal" class="form-label">Recipient Name</label>
                                        <input type="text" class="form-control" id="recipient_name_internal" 
                                               name="recipient_name" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="amount_internal" class="form-label">Amount</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><?php echo CURRENCY_SYMBOL; ?></span>
                                            <input type="number" class="form-control" id="amount_internal" 
                                                   name="amount" step="0.01" min="0.01" 
                                                   max="<?php echo $account['balance']; ?>" required>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="description_internal" class="form-label">Description (Optional)</label>
                                        <textarea class="form-control" id="description_internal" 
                                                  name="description" rows="2"></textarea>
                                    </div>
                                    
                                    <div class="alert alert-info">
                                        <i class="bi bi-info-circle"></i> Internal transfers require admin approval before processing.
                                    </div>
                                    
                                    <div class="d-grid gap-2">
                                        <button type="submit" class="btn btn-success">
                                            <i class="bi bi-check-circle"></i> Submit Transfer Request
                                        </button>
                                        <a href="dashboard.php" class="btn btn-outline-secondary">Cancel</a>
                                    </div>
                                </form>
                            </div>
                            
                            <!-- External Transfer -->
                            <div class="tab-pane fade" id="external" role="tabpanel">
                                <form method="POST" action="">
                                    <input type="hidden" name="transfer_type" value="external">
                                    
                                    <div class="mb-3">
                                        <label for="recipient_name_external" class="form-label">Recipient Name</label>
                                        <input type="text" class="form-control" id="recipient_name_external" 
                                               name="recipient_name" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="recipient_account_external" class="form-label">Recipient Account Number</label>
                                        <input type="text" class="form-control" id="recipient_account_external" 
                                               name="recipient_account" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="recipient_bank" class="form-label">Recipient Bank Name</label>
                                        <input type="text" class="form-control" id="recipient_bank" 
                                               name="recipient_bank" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="amount_external" class="form-label">Amount</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><?php echo CURRENCY_SYMBOL; ?></span>
                                            <input type="number" class="form-control" id="amount_external" 
                                                   name="amount" step="0.01" min="0.01" 
                                                   max="<?php echo $account['balance']; ?>" required>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="description_external" class="form-label">Description (Optional)</label>
                                        <textarea class="form-control" id="description_external" 
                                                  name="description" rows="2"></textarea>
                                    </div>
                                    
                                    <div class="alert alert-warning">
                                        <i class="bi bi-exclamation-triangle"></i> External transfers require admin approval and may take longer to process.
                                    </div>
                                    
                                    <div class="d-grid gap-2">
                                        <button type="submit" class="btn btn-success">
                                            <i class="bi bi-check-circle"></i> Submit Transfer Request
                                        </button>
                                        <a href="dashboard.php" class="btn btn-outline-secondary">Cancel</a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

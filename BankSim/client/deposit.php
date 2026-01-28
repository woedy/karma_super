<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/Auth.php';
require_once __DIR__ . '/../includes/Database.php';
require_once __DIR__ . '/../includes/functions.php';

Auth::requireClient();

$db = Database::getInstance();
$accountId = Auth::getAccountId();

// Get account status
$account = $db->queryOne("SELECT status FROM accounts WHERE id = ?", [$accountId]);

if ($account['status'] === 'SUSPENDED') {
    setFlashMessage('error', 'Your account has been restricted. Please contact support at support@banksim.com or call 1-800-BANKSIM to resolve this situation.');
    redirect('/BankSim/client/dashboard.php');
}

// Handle form submission
if (isPost()) {
    $amount = getPost('amount');
    $description = sanitizeInput(getPost('description'));
    
    if (!validateAmount($amount)) {
        setFlashMessage('error', 'Please enter a valid amount');
    } else {
        // Create deposit transaction
        $result = $db->execute("
            INSERT INTO transactions (account_id, transaction_type, amount, description, status, created_at)
            VALUES (?, 'DEPOSIT', ?, ?, 'PENDING', NOW())
        ", [$accountId, $amount, $description]);
        
        if ($result) {
            $transactionId = $db->lastInsertId();
            setFlashMessage('success', "Deposit request submitted successfully! Reference: #$transactionId");
            redirect('/BankSim/client/dashboard.php');
        } else {
            setFlashMessage('error', 'Failed to submit deposit request');
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deposit - <?php echo APP_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/client.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="bi bi-plus-circle"></i> Deposit Request</h5>
                    </div>
                    <div class="card-body">
                        <?php displayFlashMessage(); ?>
                        
                        <form method="POST" action="" id="depositForm">
                            <div class="mb-3">
                                <label for="amount" class="form-label">Amount</label>
                                <div class="input-group">
                                    <span class="input-group-text"><?php echo CURRENCY_SYMBOL; ?></span>
                                    <input type="number" class="form-control" id="amount" name="amount" 
                                           step="0.01" min="0.01" required>
                                </div>
                                <small class="text-muted">Enter the amount you want to deposit</small>
                            </div>
                            
                            <div class="mb-3">
                                <label for="description" class="form-label">Description (Optional)</label>
                                <textarea class="form-control" id="description" name="description" 
                                          rows="3" placeholder="Add a note about this deposit"></textarea>
                            </div>
                            
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle"></i> Your deposit request will be reviewed by an admin before being processed.
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-circle"></i> Submit Deposit Request
                                </button>
                                <a href="dashboard.php" class="btn btn-outline-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/Auth.php';
require_once __DIR__ . '/../includes/Database.php';
require_once __DIR__ . '/../includes/functions.php';

Auth::requireAdmin();

$db = Database::getInstance();
$errors = [];

// Get all clients with active accounts for the dropdown
$clients = $db->query("
    SELECT c.id, c.full_name, a.id as account_id, a.account_number, a.balance 
    FROM clients c 
    JOIN accounts a ON c.id = a.client_id 
    WHERE a.status = 'ACTIVE' 
    ORDER BY c.full_name ASC
");

if (isPost()) {
    $accountId = getPost('account_id');
    $type = getPost('transaction_type');
    $amount = getPost('amount');
    $description = trim(getPost('description'));
    $recipientName = trim(getPost('recipient_name'));
    $recipientAccount = trim(getPost('recipient_account'));
    $recipientBank = trim(getPost('recipient_bank'));
    
    // Validation
    if (empty($accountId)) {
        $errors[] = "Client account is required";
    }
    
    if (empty($type)) {
        $errors[] = "Transaction type is required";
    }
    
    if (empty($amount) || !is_numeric($amount) || $amount <= 0) {
        $errors[] = "Valid amount is required";
    }
    
    // Additional validation for transfers
    if ($type === 'INTERNAL_TRANSFER' || $type === 'EXTERNAL_TRANSFER') {
        if (empty($recipientName)) $errors[] = "Recipient name is required";
        if (empty($recipientAccount)) $errors[] = "Recipient account is required";
        if ($type === 'EXTERNAL_TRANSFER' && empty($recipientBank)) $errors[] = "Recipient bank is required";
        
        // Check sufficient balance for withdrawals and transfers
        if ($accountId) {
            $account = $db->queryOne("SELECT balance FROM accounts WHERE id = ?", [$accountId]);
            if ($account && $amount > $account['balance']) {
                $errors[] = "Insufficient balance for this transaction";
            }
        }
    } elseif ($type === 'WITHDRAWAL') {
        // Check sufficient balance for withdrawals
        if ($accountId) {
            $account = $db->queryOne("SELECT balance FROM accounts WHERE id = ?", [$accountId]);
            if ($account && $amount > $account['balance']) {
                $errors[] = "Insufficient balance for withdrawal";
            }
        }
    }
    
    if (empty($errors)) {
        try {
            $sql = "INSERT INTO transactions (account_id, transaction_type, amount, STATUS, description, 
                    recipient_name, recipient_account, recipient_bank, created_at) 
                    VALUES (?, ?, ?, 'PENDING', ?, ?, ?, ?, NOW())";
            
            $params = [
                $accountId,
                $type,
                $amount,
                $description,
                $recipientName,
                $recipientAccount,
                $recipientBank
            ];
            
            $db->execute($sql, $params);
            
            setFlashMessage('success', "Transaction created successfully and is pending approval");
            redirect('/BankSim/admin/transactions.php');
            
        } catch (Exception $e) {
            $errors[] = "Error creating transaction: " . $e->getMessage();
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
    <title>Add Transaction - <?php echo APP_NAME; ?></title>
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
                    <h1 class="h2">Add New Transaction</h1>
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
                                    <label for="account_id" class="form-label">Client Account <span class="text-danger">*</span></label>
                                    <select class="form-select" id="account_id" name="account_id" required>
                                        <option value="">Select Client Account</option>
                                        <?php foreach ($clients as $client): ?>
                                            <option value="<?php echo $client['account_id']; ?>" 
                                                data-balance="<?php echo $client['balance']; ?>"
                                                <?php echo getPost('account_id') == $client['account_id'] ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($client['full_name']); ?> - <?php echo htmlspecialchars($client['account_number']); ?> (<?php echo formatCurrency($client['balance']); ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="transaction_type" class="form-label">Transaction Type <span class="text-danger">*</span></label>
                                    <select class="form-select" id="transaction_type" name="transaction_type" required onchange="toggleRecipientFields()">
                                        <option value="">Select Type</option>
                                        <option value="DEPOSIT" <?php echo getPost('transaction_type') === 'DEPOSIT' ? 'selected' : ''; ?>>Deposit</option>
                                        <option value="WITHDRAWAL" <?php echo getPost('transaction_type') === 'WITHDRAWAL' ? 'selected' : ''; ?>>Withdrawal</option>
                                        <option value="INTERNAL_TRANSFER" <?php echo getPost('transaction_type') === 'INTERNAL_TRANSFER' ? 'selected' : ''; ?>>Internal Transfer</option>
                                        <option value="EXTERNAL_TRANSFER" <?php echo getPost('transaction_type') === 'EXTERNAL_TRANSFER' ? 'selected' : ''; ?>>External Transfer</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="amount" class="form-label">Amount <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><?php echo CURRENCY_SYMBOL; ?></span>
                                        <input type="number" step="0.01" min="0.01" class="form-control" id="amount" name="amount" 
                                               value="<?php echo htmlspecialchars(getPost('amount', '')); ?>" required>
                                    </div>
                                    <small class="text-muted" id="balanceWarning" style="display:none; color: red !important;"></small>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <input type="text" class="form-control" id="description" name="description" 
                                           value="<?php echo htmlspecialchars(getPost('description', '')); ?>" placeholder="e.g., Monthly Rent, Salary Deposit">
                                </div>
                            </div>
                            
                            <div id="recipientFields" style="display: none;">
                                <h5 class="mt-3 mb-3 border-bottom pb-2">Recipient Details</h5>
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="recipient_name" class="form-label">Recipient Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="recipient_name" name="recipient_name" 
                                               value="<?php echo htmlspecialchars(getPost('recipient_name', '')); ?>">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="recipient_account" class="form-label">Recipient Account No. <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="recipient_account" name="recipient_account" 
                                               value="<?php echo htmlspecialchars(getPost('recipient_account', '')); ?>">
                                    </div>
                                    <div class="col-md-4 mb-3" id="bankField" style="display: none;">
                                        <label for="recipient_bank" class="form-label">Recipient Bank <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="recipient_bank" name="recipient_bank" 
                                               value="<?php echo htmlspecialchars(getPost('recipient_bank', '')); ?>">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="d-flex gap-2 mt-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-circle"></i> Create Transaction
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
        function toggleRecipientFields() {
            const type = document.getElementById('transaction_type').value;
            const recipientFields = document.getElementById('recipientFields');
            const bankField = document.getElementById('bankField');
            const recipientInputs = recipientFields.querySelectorAll('input');
            
            if (type === 'INTERNAL_TRANSFER' || type === 'EXTERNAL_TRANSFER') {
                recipientFields.style.display = 'block';
                recipientInputs.forEach(input => input.required = true);
                
                if (type === 'EXTERNAL_TRANSFER') {
                    bankField.style.display = 'block';
                } else {
                    bankField.style.display = 'none';
                    document.getElementById('recipient_bank').required = false;
                }
            } else {
                recipientFields.style.display = 'none';
                recipientInputs.forEach(input => input.required = false);
            }
        }
        
        // Initial check
        document.addEventListener('DOMContentLoaded', toggleRecipientFields);
        
        // Client-side balance check
        document.getElementById('amount').addEventListener('input', checkBalance);
        document.getElementById('transaction_type').addEventListener('change', checkBalance);
        document.getElementById('account_id').addEventListener('change', checkBalance);
        
        function checkBalance() {
            const accountSelect = document.getElementById('account_id');
            const amountInput = document.getElementById('amount');
            const typeSelect = document.getElementById('transaction_type');
            const warningEl = document.getElementById('balanceWarning');
            
            if (accountSelect.selectedIndex > 0 && amountInput.value && typeSelect.value) {
                const selectedOption = accountSelect.options[accountSelect.selectedIndex];
                const balance = parseFloat(selectedOption.getAttribute('data-balance'));
                const amount = parseFloat(amountInput.value);
                const type = typeSelect.value;
                
                if ((type === 'WITHDRAWAL' || type.includes('TRANSFER')) && amount > balance) {
                    warningEl.textContent = `Warning: Amount exceeds current balance ($${balance.toFixed(2)})`;
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

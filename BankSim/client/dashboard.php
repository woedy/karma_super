<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/Auth.php';
require_once __DIR__ . '/../includes/Database.php';
require_once __DIR__ . '/../includes/functions.php';

Auth::requireClient();

$db = Database::getInstance();
$accountId = Auth::getAccountId();

// Get account details
$account = $db->queryOne("
    SELECT a.*, c.full_name, c.email
    FROM accounts a
    JOIN clients c ON a.client_id = c.id
    WHERE a.id = ?
", [$accountId]);

// Get recent transactions
$recentTransactions = $db->query("
    SELECT * FROM transactions
    WHERE account_id = ?
    ORDER BY created_at DESC
    LIMIT 5
", [$accountId]);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - <?php echo APP_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/client.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <h2 class="mb-4">Welcome, <?php echo htmlspecialchars($_SESSION['client_name']); ?>!</h2>
            </div>
        </div>
        
        <?php displayFlashMessage(); ?>
        
        <!-- Account Balance Card -->
        <div class="row mb-4">
            <div class="col-md-8">
                <div class="card balance-card">
                    <div class="card-body p-4">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <h6 class="text-muted mb-2">Available Balance</h6>
                                <h1 class="display-4 fw-bold text-success mb-0"><?php echo formatCurrency($account['balance']); ?></h1>
                                <p class="text-muted mt-2 mb-0">
                                    <small>Account: <?php echo htmlspecialchars($account['account_number']); ?></small>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <div class="d-grid gap-2">
                                    <a href="deposit.php" class="btn btn-primary">
                                        <i class="bi bi-plus-circle"></i> Deposit
                                    </a>
                                    <a href="withdraw.php" class="btn btn-warning">
                                        <i class="bi bi-dash-circle"></i> Withdraw
                                    </a>
                                    <a href="transfer.php" class="btn btn-success">
                                        <i class="bi bi-arrow-left-right"></i> Transfer
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-subtitle mb-3 text-muted">Account Information</h6>
                        <p class="mb-2"><strong>Type:</strong> <?php echo htmlspecialchars($account['account_type']); ?></p>
                        <p class="mb-2"><strong>Status:</strong> 
                            <?php if ($account['status'] === 'ACTIVE'): ?>
                                <span class="badge bg-success">Active</span>
                            <?php elseif ($account['status'] === 'SUSPENDED'): ?>
                                <span class="badge bg-danger">Suspended</span>
                            <?php else: ?>
                                <span class="badge bg-secondary"><?php echo htmlspecialchars($account['status']); ?></span>
                            <?php endif; ?>
                        </p>
                        <p class="mb-0"><strong>Email:</strong> <?php echo htmlspecialchars($account['email']); ?></p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Recent Transactions -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Recent Transactions</h5>
                        <a href="transactions.php" class="btn btn-sm btn-outline-primary">View All</a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Type</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($recentTransactions)): ?>
                                        <tr>
                                            <td colspan="5" class="text-center text-muted">No transactions yet</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($recentTransactions as $trans): ?>
                                            <tr>
                                                <td><?php echo formatDate($trans['created_at']); ?></td>
                                                <td><?php echo getTransactionTypeLabel($trans['transaction_type']); ?></td>
                                                <td class="fw-bold">
                                                    <?php if (in_array($trans['transaction_type'], ['WITHDRAWAL', 'INTERNAL_TRANSFER', 'EXTERNAL_TRANSFER'])): ?>
                                                        <span class="text-danger">-<?php echo formatCurrency($trans['amount']); ?></span>
                                                    <?php else: ?>
                                                        <span class="text-success">+<?php echo formatCurrency($trans['amount']); ?></span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?php echo getStatusBadge($trans['status']); ?></td>
                                                <td><?php echo htmlspecialchars($trans['description'] ?? 'N/A'); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

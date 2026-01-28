<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/Auth.php';
require_once __DIR__ . '/../includes/Database.php';
require_once __DIR__ . '/../includes/functions.php';

Auth::requireClient();

$db = Database::getInstance();
$accountId = Auth::getAccountId();

// Get filter parameters
$filterStatus = getGet('status', 'all');
$filterType = getGet('type', 'all');

// Build query
$sql = "SELECT * FROM transactions WHERE account_id = ?";
$params = [$accountId];

if ($filterStatus !== 'all') {
    $sql .= " AND status = ?";
    $params[] = $filterStatus;
}

if ($filterType !== 'all') {
    $sql .= " AND transaction_type = ?";
    $params[] = $filterType;
}

$sql .= " ORDER BY created_at DESC";

$transactions = $db->query($sql, $params);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transactions - <?php echo APP_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/client.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <h2 class="mb-4">Transaction History</h2>
            </div>
        </div>
        
        <?php displayFlashMessage(); ?>
        
        <!-- Filters -->
        <div class="card mb-3">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-4">
                        <label for="status" class="form-label">Filter by Status</label>
                        <select name="status" id="status" class="form-select" onchange="this.form.submit()">
                            <option value="all" <?php echo $filterStatus === 'all' ? 'selected' : ''; ?>>All Status</option>
                            <option value="PENDING" <?php echo $filterStatus === 'PENDING' ? 'selected' : ''; ?>>Pending</option>
                            <option value="COMPLETED" <?php echo $filterStatus === 'COMPLETED' ? 'selected' : ''; ?>>Completed</option>
                            <option value="REJECTED" <?php echo $filterStatus === 'REJECTED' ? 'selected' : ''; ?>>Rejected</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="type" class="form-label">Filter by Type</label>
                        <select name="type" id="type" class="form-select" onchange="this.form.submit()">
                            <option value="all" <?php echo $filterType === 'all' ? 'selected' : ''; ?>>All Types</option>
                            <option value="DEPOSIT" <?php echo $filterType === 'DEPOSIT' ? 'selected' : ''; ?>>Deposits</option>
                            <option value="WITHDRAWAL" <?php echo $filterType === 'WITHDRAWAL' ? 'selected' : ''; ?>>Withdrawals</option>
                            <option value="INTERNAL_TRANSFER" <?php echo $filterType === 'INTERNAL_TRANSFER' ? 'selected' : ''; ?>>Internal Transfers</option>
                            <option value="EXTERNAL_TRANSFER" <?php echo $filterType === 'EXTERNAL_TRANSFER' ? 'selected' : ''; ?>>External Transfers</option>
                        </select>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Transactions Table -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">All Transactions</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Amount</th>
                                <th>Details</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($transactions)): ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">No transactions found</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($transactions as $trans): ?>
                                    <tr>
                                        <td>#<?php echo $trans['id']; ?></td>
                                        <td><?php echo formatDate($trans['created_at']); ?></td>
                                        <td><?php echo getTransactionTypeLabel($trans['transaction_type']); ?></td>
                                        <td class="fw-bold">
                                            <?php if (in_array($trans['transaction_type'], ['WITHDRAWAL', 'INTERNAL_TRANSFER', 'EXTERNAL_TRANSFER'])): ?>
                                                <span class="text-danger">-<?php echo formatCurrency($trans['amount']); ?></span>
                                            <?php else: ?>
                                                <span class="text-success">+<?php echo formatCurrency($trans['amount']); ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($trans['recipient_account']): ?>
                                                <small>
                                                    To: <?php echo htmlspecialchars($trans['recipient_name']); ?><br>
                                                    Acc: <?php echo htmlspecialchars($trans['recipient_account']); ?>
                                                    <?php if ($trans['recipient_bank']): ?>
                                                        <br>Bank: <?php echo htmlspecialchars($trans['recipient_bank']); ?>
                                                    <?php endif; ?>
                                                </small>
                                            <?php endif; ?>
                                            <?php if ($trans['description']): ?>
                                                <small class="text-muted d-block"><?php echo htmlspecialchars($trans['description']); ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo getStatusBadge($trans['status']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/Auth.php';
require_once __DIR__ . '/../includes/Database.php';
require_once __DIR__ . '/../includes/functions.php';

Auth::requireAdmin();

$db = Database::getInstance();

// Get filter parameters
$filterStatus = getGet('status', 'all');
$filterType = getGet('type', 'all');

// Build query
$sql = "
    SELECT t.*, a.account_number, c.full_name as client_name
    FROM transactions t
    JOIN accounts a ON t.account_id = a.id
    JOIN clients c ON a.client_id = c.id
    WHERE 1=1
";

$params = [];

if ($filterStatus !== 'all') {
    $sql .= " AND t.status = :status";
    $params['status'] = $filterStatus;
}

if ($filterType !== 'all') {
    $sql .= " AND t.transaction_type = :type";
    $params['type'] = $filterType;
}

$sql .= " ORDER BY t.created_at DESC LIMIT 100";

$transactions = $db->query($sql, $params);
$pendingCount = $db->queryOne("SELECT COUNT(*) as count FROM transactions WHERE status = 'PENDING'")['count'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Transactions - <?php echo APP_NAME; ?></title>
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
                    <h1 class="h2">All Transactions</h1>
                    <a href="/BankSim/admin/add_transaction.php" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Add New Transaction
                    </a>
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
                        <h5 class="mb-0">Transaction History (Last 100)</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Client</th>
                                        <th>Account</th>
                                        <th>Type</th>
                                        <th>Amount</th>
                                        <th>Details</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($transactions)): ?>
                                        <tr>
                                            <td colspan="9" class="text-center text-muted py-4">No transactions found</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($transactions as $trans): ?>
                                            <tr>
                                                <td>#<?php echo $trans['id']; ?></td>
                                                <td><?php echo htmlspecialchars($trans['client_name']); ?></td>
                                                <td><?php echo htmlspecialchars($trans['account_number']); ?></td>
                                                <td><?php echo getTransactionTypeLabel($trans['transaction_type']); ?></td>
                                                <td class="fw-bold"><?php echo formatCurrency($trans['amount']); ?></td>
                                                <td>
                                                    <?php if ($trans['recipient_account']): ?>
                                                        <small>
                                                            To: <?php echo htmlspecialchars($trans['recipient_name']); ?><br>
                                                            Acc: <?php echo htmlspecialchars($trans['recipient_account']); ?>
                                                        </small>
                                                    <?php endif; ?>
                                                    <?php if ($trans['description']): ?>
                                                        <small class="text-muted d-block"><?php echo htmlspecialchars($trans['description']); ?></small>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?php echo getStatusBadge($trans['status']); ?></td>
                                                <td><?php echo formatDate($trans['created_at']); ?></td>
                                                <td>
                                                    <?php if ($trans['status'] === 'PENDING'): ?>
                                                        <div class="btn-group btn-group-sm" role="group">
                                                            <a href="/BankSim/admin/edit_transaction.php?id=<?php echo $trans['id']; ?>" 
                                                               class="btn btn-outline-primary" title="Edit">
                                                                <i class="bi bi-pencil"></i>
                                                            </a>
                                                            <button type="button" class="btn btn-outline-danger" 
                                                                    onclick="confirmDeleteTransaction(<?php echo $trans['id']; ?>, '<?php echo htmlspecialchars($trans['transaction_type'], ENT_QUOTES); ?>')"
                                                                    title="Delete">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </div>
                                                    <?php else: ?>
                                                        <span class="text-muted">-</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    
    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteTransactionModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this <strong id="transactionType"></strong> transaction?</p>
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle"></i>
                        This action cannot be undone.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteTransactionBtn">Delete</button>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let deleteTransactionId = null;
        const deleteTransactionModal = new bootstrap.Modal(document.getElementById('deleteTransactionModal'));
        
        function confirmDeleteTransaction(transactionId, transactionType) {
            deleteTransactionId = transactionId;
            document.getElementById('transactionType').textContent = transactionType;
            deleteTransactionModal.show();
        }
        
        document.getElementById('confirmDeleteTransactionBtn').addEventListener('click', function() {
            if (deleteTransactionId) {
                fetch('/BankSim/admin/delete_transaction.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'id=' + deleteTransactionId
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    alert('Error deleting transaction');
                    console.error('Error:', error);
                });
                
                deleteTransactionModal.hide();
            }
        });
    </script>
</body>
</html>

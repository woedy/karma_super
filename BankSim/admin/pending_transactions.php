<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/Auth.php';
require_once __DIR__ . '/../includes/Database.php';
require_once __DIR__ . '/../includes/functions.php';

Auth::requireAdmin();

$db = Database::getInstance();

// Get filter parameters
$filterType = getGet('type', 'all');

// Build query
$sql = "
    SELECT t.*, a.account_number, c.full_name as client_name
    FROM transactions t
    JOIN accounts a ON t.account_id = a.id
    JOIN clients c ON a.client_id = c.id
    WHERE t.status = 'PENDING'
";

if ($filterType !== 'all') {
    $sql .= " AND t.transaction_type = :type";
}

$sql .= " ORDER BY t.created_at ASC";

$params = [];
if ($filterType !== 'all') {
    $params['type'] = $filterType;
}

$pendingTransactions = $db->query($sql, $params);
$pendingCount = count($pendingTransactions);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pending Transactions - <?php echo APP_NAME; ?></title>
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
                    <h1 class="h2">Pending Transactions</h1>
                </div>
                
                <?php displayFlashMessage(); ?>
                
                <!-- Filter -->
                <div class="card mb-3">
                    <div class="card-body">
                        <form method="GET" class="row g-3">
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
                        <h5 class="mb-0">Pending Approvals (<?php echo $pendingCount; ?>)</h5>
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
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($pendingTransactions)): ?>
                                        <tr>
                                            <td colspan="8" class="text-center text-muted py-4">No pending transactions</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($pendingTransactions as $trans): ?>
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
                                                            <?php if ($trans['recipient_bank']): ?>
                                                                <br>Bank: <?php echo htmlspecialchars($trans['recipient_bank']); ?>
                                                            <?php endif; ?>
                                                        </small>
                                                    <?php endif; ?>
                                                    <?php if ($trans['description']): ?>
                                                        <small class="text-muted d-block"><?php echo htmlspecialchars($trans['description']); ?></small>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?php echo formatDate($trans['created_at']); ?></td>
                                                <td>
                                                    <button class="btn btn-sm btn-success" onclick="approveTransaction(<?php echo $trans['id']; ?>, 'approve')">
                                                        <i class="bi bi-check-circle"></i> Approve
                                                    </button>
                                                    <button class="btn btn-sm btn-danger" onclick="approveTransaction(<?php echo $trans['id']; ?>, 'reject')">
                                                        <i class="bi bi-x-circle"></i> Reject
                                                    </button>
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
    
    <!-- Approval/Rejection Confirmation Modal -->
    <div class="modal fade" id="confirmActionModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Confirm Action</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p id="modalMessage">Are you sure you want to proceed?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn" id="confirmBtn">Confirm</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const confirmModal = new bootstrap.Modal(document.getElementById('confirmActionModal'));
        let targetUrl = '';

        function approveTransaction(transactionId, action) {
            const actionText = action === 'approve' ? 'approve' : 'reject';
            const btnClass = action === 'approve' ? 'btn-success' : 'btn-danger';
            
            document.getElementById('modalTitle').textContent = action === 'approve' ? 'Approve Transaction' : 'Reject Transaction';
            document.getElementById('modalMessage').innerHTML = `Are you sure you want to <strong>${actionText}</strong> this transaction? <br><small class="text-muted">This action cannot be undone.</small>`;
            
            const confirmBtn = document.getElementById('confirmBtn');
            confirmBtn.textContent = action === 'approve' ? 'Yes, Approve' : 'Yes, Reject';
            confirmBtn.className = `btn ${btnClass}`;
            
            targetUrl = `approve_transaction.php?id=${transactionId}&action=${action}`;
            confirmModal.show();
        }
        
        document.getElementById('confirmBtn').addEventListener('click', function() {
            if (targetUrl) {
                window.location.href = targetUrl;
            }
        });
    </script>
</body>
</html>

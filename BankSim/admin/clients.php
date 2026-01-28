<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/Auth.php';
require_once __DIR__ . '/../includes/Database.php';
require_once __DIR__ . '/../includes/functions.php';

Auth::requireAdmin();

$db = Database::getInstance();

// Get all clients with their accounts
$clients = $db->query("
    SELECT c.*, a.account_number, a.balance, a.account_type, a.status as account_status
    FROM clients c
    LEFT JOIN accounts a ON c.id = a.client_id
    ORDER BY c.created_at DESC
");

$pendingCount = $db->queryOne("SELECT COUNT(*) as count FROM transactions WHERE status = 'PENDING'")['count'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clients - <?php echo APP_NAME; ?></title>
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
                    <h1 class="h2">Clients</h1>
                    <a href="/BankSim/admin/add_client.php" class="btn btn-primary">
                        <i class="bi bi-person-plus"></i> Add New Client
                    </a>
                </div>
                
                <?php displayFlashMessage(); ?>
                
                <!-- Clients Table -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">All Clients</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Full Name</th>
                                        <th>Username</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Account Number</th>
                                        <th>Balance</th>
                                        <th>Account Type</th>
                                        <th>Status</th>
                                        <th>Joined</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($clients)): ?>
                                        <tr>
                                            <td colspan="11" class="text-center text-muted py-4">No clients found</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($clients as $client): ?>
                                            <tr>
                                                <td>#<?php echo $client['id']; ?></td>
                                                <td><?php echo htmlspecialchars($client['full_name']); ?></td>
                                                <td><?php echo htmlspecialchars($client['username']); ?></td>
                                                <td><?php echo htmlspecialchars($client['email']); ?></td>
                                                <td><?php echo htmlspecialchars($client['phone'] ?? 'N/A'); ?></td>
                                                <td><?php echo htmlspecialchars($client['account_number'] ?? 'N/A'); ?></td>
                                                <td class="fw-bold"><?php echo $client['balance'] !== null ? formatCurrency($client['balance']) : 'N/A'; ?></td>
                                                <td><?php echo htmlspecialchars($client['account_type'] ?? 'N/A'); ?></td>
                                                <td>
                                                    <?php if ($client['account_status'] === 'ACTIVE'): ?>
                                                        <span class="badge bg-success">Active</span>
                                                    <?php elseif ($client['account_status'] === 'SUSPENDED'): ?>
                                                        <span class="badge bg-danger">Suspended</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-secondary">No Account</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?php echo formatDate($client['created_at']); ?></td>
                                                <td>
                                                    <div class="btn-group btn-group-sm" role="group">
                                                        <a href="/BankSim/admin/edit_client.php?id=<?php echo $client['id']; ?>" 
                                                           class="btn btn-outline-primary" title="Edit">
                                                            <i class="bi bi-pencil"></i>
                                                        </a>
                                                        <button type="button" class="btn btn-outline-danger" 
                                                                onclick="confirmDelete(<?php echo $client['id']; ?>, '<?php echo htmlspecialchars($client['full_name'], ENT_QUOTES); ?>')"
                                                                title="Delete">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </div>
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
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete client <strong id="clientName"></strong>?</p>
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle"></i>
                        This will permanently delete the client, their account, and all associated transactions.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let deleteClientId = null;
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        
        function confirmDelete(clientId, clientName) {
            deleteClientId = clientId;
            document.getElementById('clientName').textContent = clientName;
            deleteModal.show();
        }
        
        document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
            if (deleteClientId) {
                // Send delete request
                fetch('/BankSim/admin/delete_client.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'id=' + deleteClientId
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
                    alert('Error deleting client');
                    console.error('Error:', error);
                });
                
                deleteModal.hide();
            }
        });
    </script>
</body>
</html>

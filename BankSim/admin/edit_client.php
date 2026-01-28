<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/Auth.php';
require_once __DIR__ . '/../includes/Database.php';
require_once __DIR__ . '/../includes/functions.php';

Auth::requireAdmin();

$db = Database::getInstance();
$errors = [];
$clientId = getGet('id');

if (empty($clientId)) {
    setFlashMessage('error', 'Client ID is required');
    redirect('/BankSim/admin/clients.php');
}

// Get client and account data
$client = $db->queryOne("
    SELECT c.*, a.id as account_id, a.account_number, a.account_type, a.status as account_status
    FROM clients c
    LEFT JOIN accounts a ON c.id = a.client_id
    WHERE c.id = ?
", [$clientId]);

if (!$client) {
    setFlashMessage('error', 'Client not found');
    redirect('/BankSim/admin/clients.php');
}

if (isPost()) {
    $fullName = trim(getPost('full_name'));
    $email = trim(getPost('email'));
    $phone = trim(getPost('phone'));
    $accountType = getPost('account_type');
    $accountStatus = getPost('account_status');
    $newPassword = getPost('new_password');
    $confirmPassword = getPost('confirm_password');
    
    // Validation
    if (empty($fullName)) {
        $errors[] = "Full name is required";
    }
    
    if (empty($email)) {
        $errors[] = "Email is required";
    } elseif (!validateEmail($email)) {
        $errors[] = "Invalid email format";
    } elseif (!checkUniqueEmail($email, $clientId)) {
        $errors[] = "Email already exists";
    }
    
    if (!empty($phone) && !validatePhone($phone)) {
        $errors[] = "Invalid phone number format";
    }
    
    // Password validation (only if changing)
    if (!empty($newPassword)) {
        if (!validatePassword($newPassword)) {
            $errors[] = "Password must be at least 6 characters";
        } elseif ($newPassword !== $confirmPassword) {
            $errors[] = "Passwords do not match";
        }
    }
    
    // If no errors, update client and account
    if (empty($errors)) {
        try {
            $db->beginTransaction();
            
            // Update client
            $clientSql = "UPDATE clients SET full_name = ?, email = ?, phone = ?";
            $clientParams = [$fullName, $email, $phone];
            
            // Add password update if provided
            if (!empty($newPassword)) {
                $clientSql .= ", password_hash = ?";
                $clientParams[] = password_hash($newPassword, PASSWORD_HASH_ALGO);
            }
            
            $clientSql .= " WHERE id = ?";
            $clientParams[] = $clientId;
            
            $db->execute($clientSql, $clientParams);
            
            // Update account if exists
            if ($client['account_id']) {
                $accountSql = "UPDATE accounts SET account_type = ?, status = ? WHERE id = ?";
                $db->execute($accountSql, [$accountType, $accountStatus, $client['account_id']]);
            }
            
            $db->commit();
            
            setFlashMessage('success', "Client '$fullName' updated successfully");
            redirect('/BankSim/admin/clients.php');
            
        } catch (Exception $e) {
            $db->rollback();
            $errors[] = "Error updating client: " . $e->getMessage();
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
    <title>Edit Client - <?php echo APP_NAME; ?></title>
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
                    <h1 class="h2">Edit Client</h1>
                    <a href="/BankSim/admin/clients.php" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Back to Clients
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
                    <div class="card-header">
                        <h5 class="mb-0">Client Information</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="full_name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="full_name" name="full_name" 
                                           value="<?php echo htmlspecialchars(getPost('full_name', $client['full_name'])); ?>" required>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="username" class="form-label">Username</label>
                                    <input type="text" class="form-control" id="username" 
                                           value="<?php echo htmlspecialchars($client['username']); ?>" disabled>
                                    <small class="text-muted">Username cannot be changed</small>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           value="<?php echo htmlspecialchars(getPost('email', $client['email'])); ?>" required>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="phone" class="form-label">Phone</label>
                                    <input type="tel" class="form-control" id="phone" name="phone" 
                                           value="<?php echo htmlspecialchars(getPost('phone', $client['phone'] ?? '')); ?>" 
                                           placeholder="e.g., 1234567890">
                                </div>
                            </div>
                            
                            <?php if ($client['account_id']): ?>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="account_number" class="form-label">Account Number</label>
                                    <input type="text" class="form-control" id="account_number" 
                                           value="<?php echo htmlspecialchars($client['account_number']); ?>" disabled>
                                    <small class="text-muted">Account number cannot be changed</small>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="account_type" class="form-label">Account Type <span class="text-danger">*</span></label>
                                    <select class="form-select" id="account_type" name="account_type" required>
                                        <?php
                                        $selectedType = getPost('account_type', $client['account_type']);
                                        ?>
                                        <option value="SAVINGS" <?php echo $selectedType === 'SAVINGS' ? 'selected' : ''; ?>>Savings</option>
                                        <option value="CHECKING" <?php echo $selectedType === 'CHECKING' ? 'selected' : ''; ?>>Checking</option>
                                        <option value="BUSINESS" <?php echo $selectedType === 'BUSINESS' ? 'selected' : ''; ?>>Business</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="account_status" class="form-label">Account Status <span class="text-danger">*</span></label>
                                    <select class="form-select" id="account_status" name="account_status" required>
                                        <?php
                                        $selectedStatus = getPost('account_status', $client['account_status']);
                                        ?>
                                        <option value="ACTIVE" <?php echo $selectedStatus === 'ACTIVE' ? 'selected' : ''; ?>>Active</option>
                                        <option value="SUSPENDED" <?php echo $selectedStatus === 'SUSPENDED' ? 'selected' : ''; ?>>Suspended</option>
                                        <option value="CLOSED" <?php echo $selectedStatus === 'CLOSED' ? 'selected' : ''; ?>>Closed</option>
                                    </select>
                                </div>
                            </div>
                            <?php endif; ?>
                            
                            <hr class="my-4">
                            
                            <h6 class="mb-3">Change Password (Optional)</h6>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="new_password" class="form-label">New Password</label>
                                    <input type="password" class="form-control" id="new_password" name="new_password">
                                    <small class="text-muted">Leave blank to keep current password</small>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="confirm_password" class="form-label">Confirm New Password</label>
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password">
                                </div>
                            </div>
                            
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-save"></i> Update Client
                                </button>
                                <a href="/BankSim/admin/clients.php" class="btn btn-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

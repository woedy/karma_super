<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/Auth.php';
require_once __DIR__ . '/../includes/Database.php';
require_once __DIR__ . '/../includes/functions.php';

Auth::requireAdmin();

$db = Database::getInstance();
$errors = [];
$success = false;

if (isPost()) {
    $fullName = trim(getPost('full_name'));
    $username = trim(getPost('username'));
    $email = trim(getPost('email'));
    $phone = trim(getPost('phone'));
    $password = getPost('password');
    $confirmPassword = getPost('confirm_password');
    $accountType = getPost('account_type', 'SAVINGS');
    
    // Validation
    if (empty($fullName)) {
        $errors[] = "Full name is required";
    }
    
    if (empty($username)) {
        $errors[] = "Username is required";
    } elseif (!checkUniqueUsername($username)) {
        $errors[] = "Username already exists";
    }
    
    if (empty($email)) {
        $errors[] = "Email is required";
    } elseif (!validateEmail($email)) {
        $errors[] = "Invalid email format";
    } elseif (!checkUniqueEmail($email)) {
        $errors[] = "Email already exists";
    }
    
    if (!empty($phone) && !validatePhone($phone)) {
        $errors[] = "Invalid phone number format";
    }
    
    if (empty($password)) {
        $errors[] = "Password is required";
    } elseif (!validatePassword($password)) {
        $errors[] = "Password must be at least 6 characters";
    } elseif ($password !== $confirmPassword) {
        $errors[] = "Passwords do not match";
    }
    
    // If no errors, create client and account
    if (empty($errors)) {
        try {
            $db->beginTransaction();
            
            // Hash password
            $passwordHash = password_hash($password, PASSWORD_HASH_ALGO);
            
            // Insert client
            $clientSql = "INSERT INTO clients (username, email, password_hash, full_name, phone) 
                         VALUES (?, ?, ?, ?, ?)";
            $db->execute($clientSql, [$username, $email, $passwordHash, $fullName, $phone]);
            $clientId = $db->lastInsertId();
            
            // Generate account number
            $accountNumber = generateAccountNumber();
            
            // Insert account
            $accountSql = "INSERT INTO accounts (client_id, account_number, balance, account_type, status) 
                          VALUES (?, ?, 0.00, ?, 'ACTIVE')";
            $db->execute($accountSql, [$clientId, $accountNumber, $accountType]);
            
            $db->commit();
            
            setFlashMessage('success', "Client '$fullName' created successfully with account number: $accountNumber");
            redirect('/BankSim/admin/clients.php');
            
        } catch (Exception $e) {
            $db->rollback();
            $errors[] = "Error creating client: " . $e->getMessage();
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
    <title>Add Client - <?php echo APP_NAME; ?></title>
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
                    <h1 class="h2">Add New Client</h1>
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
                                           value="<?php echo htmlspecialchars(getPost('full_name', '')); ?>" required>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="username" name="username" 
                                           value="<?php echo htmlspecialchars(getPost('username', '')); ?>" required>
                                    <small class="text-muted">Used for login</small>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           value="<?php echo htmlspecialchars(getPost('email', '')); ?>" required>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="phone" class="form-label">Phone</label>
                                    <input type="tel" class="form-control" id="phone" name="phone" 
                                           value="<?php echo htmlspecialchars(getPost('phone', '')); ?>" 
                                           placeholder="e.g., 1234567890">
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control" id="password" name="password" required>
                                    <small class="text-muted">Minimum 6 characters</small>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="confirm_password" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="account_type" class="form-label">Account Type <span class="text-danger">*</span></label>
                                    <select class="form-select" id="account_type" name="account_type" required>
                                        <option value="SAVINGS" <?php echo getPost('account_type') === 'SAVINGS' ? 'selected' : ''; ?>>Savings</option>
                                        <option value="CHECKING" <?php echo getPost('account_type') === 'CHECKING' ? 'selected' : ''; ?>>Checking</option>
                                        <option value="BUSINESS" <?php echo getPost('account_type') === 'BUSINESS' ? 'selected' : ''; ?>>Business</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle"></i> 
                                An account number will be automatically generated for this client.
                            </div>
                            
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-person-plus"></i> Create Client
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

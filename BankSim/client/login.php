<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/Auth.php';
require_once __DIR__ . '/../includes/functions.php';

// Redirect if already logged in
if (Auth::isClientLoggedIn()) {
    redirect('/BankSim/client/dashboard.php');
}

// Handle login form submission
if (isPost()) {
    $username = sanitizeInput(getPost('username'));
    $password = getPost('password');
    
    if (empty($username) || empty($password)) {
        setFlashMessage('error', 'Please enter both username and password');
    } else {
        if (Auth::loginClient($username, $password)) {
            redirect('/BankSim/client/dashboard.php');
        } else {
            setFlashMessage('error', 'Invalid username or password');
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Login - <?php echo APP_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/client.css">
</head>
<body class="client-login">
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-md-5">
                <div class="card shadow-lg">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <h2 class="fw-bold text-success">Client Portal</h2>
                            <p class="text-muted"><?php echo APP_NAME; ?></p>
                        </div>
                        
                        <?php displayFlashMessage(); ?>
                        
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="username" name="username" required autofocus>
                            </div>
                            
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            
                            <button type="submit" class="btn btn-success w-100 py-2">Login</button>
                        </form>
                        
                        <div class="text-center mt-4">
                            <a href="/BankSim/" class="text-muted small">← Back to Home</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

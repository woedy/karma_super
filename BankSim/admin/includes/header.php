<nav class="navbar navbar-dark bg-dark sticky-top">
    <div class="container-fluid">
        <a class="navbar-brand" href="/BankSim/admin/dashboard.php">
            <i class="bi bi-bank2"></i> <?php echo APP_NAME; ?> - Admin
        </a>
        <div class="d-flex align-items-center text-white">
            <span class="me-3">
                <i class="bi bi-person-circle"></i> <?php echo htmlspecialchars($_SESSION['admin_name']); ?>
            </span>
            <a href="/BankSim/admin/logout.php" class="btn btn-outline-light btn-sm">
                <i class="bi bi-box-arrow-right"></i> Logout
            </a>
        </div>
    </div>
</nav>

<nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
    <div class="position-sticky pt-3">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>" href="/BankSim/admin/dashboard.php">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'pending_transactions.php' ? 'active' : ''; ?>" href="/BankSim/admin/pending_transactions.php">
                    <i class="bi bi-clock-history"></i> Pending Transactions
                    <?php if (isset($pendingCount) && $pendingCount > 0): ?>
                        <span class="badge bg-warning text-dark"><?php echo $pendingCount; ?></span>
                    <?php endif; ?>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'transactions.php' ? 'active' : ''; ?>" href="/BankSim/admin/transactions.php">
                    <i class="bi bi-list-ul"></i> All Transactions
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'clients.php' ? 'active' : ''; ?>" href="/BankSim/admin/clients.php">
                    <i class="bi bi-people"></i> Clients
                </a>
            </li>
        </ul>
    </div>
</nav>

<?php
/**
 * Helper Functions
 * Common utility functions used throughout the application
 */

/**
 * Sanitize input data
 */
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

/**
 * Format currency
 */
function formatCurrency($amount) {
    return CURRENCY_SYMBOL . number_format($amount, 2);
}

/**
 * Generate unique account number
 */
function generateAccountNumber() {
    return 'ACC' . time() . rand(1000, 9999);
}

/**
 * Set flash message
 */
function setFlashMessage($type, $message) {
    $_SESSION['flash_message'] = [
        'type' => $type, // success, error, warning, info
        'message' => $message
    ];
}

/**
 * Get and clear flash message
 */
function getFlashMessage() {
    if (isset($_SESSION['flash_message'])) {
        $flash = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        return $flash;
    }
    return null;
}

/**
 * Display flash message HTML
 */
function displayFlashMessage() {
    $flash = getFlashMessage();
    if ($flash) {
        $type = $flash['type'];
        $message = $flash['message'];
        
        $alertClass = [
            'success' => 'alert-success',
            'error' => 'alert-danger',
            'warning' => 'alert-warning',
            'info' => 'alert-info'
        ];
        
        $class = $alertClass[$type] ?? 'alert-info';
        
        echo '<div class="alert ' . $class . ' alert-dismissible fade show" role="alert">';
        echo htmlspecialchars($message);
        echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
        echo '</div>';
    }
}

/**
 * Validate amount
 */
function validateAmount($amount) {
    return is_numeric($amount) && $amount > 0;
}

/**
 * Format date
 */
function formatDate($date) {
    return date('M d, Y h:i A', strtotime($date));
}

/**
 * Get transaction status badge
 */
function getStatusBadge($status) {
    $badges = [
        'PENDING' => '<span class="badge bg-warning">Pending</span>',
        'APPROVED' => '<span class="badge bg-info">Approved</span>',
        'COMPLETED' => '<span class="badge bg-success">Completed</span>',
        'REJECTED' => '<span class="badge bg-danger">Rejected</span>'
    ];
    
    return $badges[$status] ?? '<span class="badge bg-secondary">' . $status . '</span>';
}

/**
 * Get transaction type label
 */
function getTransactionTypeLabel($type) {
    $labels = [
        'DEPOSIT' => 'Deposit',
        'WITHDRAWAL' => 'Withdrawal',
        'INTERNAL_TRANSFER' => 'Internal Transfer',
        'EXTERNAL_TRANSFER' => 'External Transfer'
    ];
    
    return $labels[$type] ?? $type;
}

/**
 * Redirect helper
 */
function redirect($url) {
    header("Location: $url");
    exit;
}

/**
 * Check if request is POST
 */
function isPost() {
    return $_SERVER['REQUEST_METHOD'] === 'POST';
}

/**
 * Get POST data
 */
function getPost($key, $default = null) {
    return $_POST[$key] ?? $default;
}

/**
 * Get GET data
 */
function getGet($key, $default = null) {
    return $_GET[$key] ?? $default;
}

/**
 * Validate email format
 */
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validate phone number (basic validation)
 */
function validatePhone($phone) {
    // Remove all non-digit characters
    $cleaned = preg_replace('/[^0-9]/', '', $phone);
    // Check if it has at least 10 digits
    return strlen($cleaned) >= 10;
}

/**
 * Validate password strength
 */
function validatePassword($password) {
    // At least 6 characters
    return strlen($password) >= 6;
}

/**
 * Check if username is unique
 */
function checkUniqueUsername($username, $excludeId = null) {
    $db = Database::getInstance();
    $sql = "SELECT COUNT(*) as count FROM clients WHERE username = ?";
    $params = [$username];
    
    if ($excludeId !== null) {
        $sql .= " AND id != ?";
        $params[] = $excludeId;
    }
    
    $result = $db->queryOne($sql, $params);
    return $result['count'] == 0;
}

/**
 * Check if email is unique
 */
function checkUniqueEmail($email, $excludeId = null) {
    $db = Database::getInstance();
    $sql = "SELECT COUNT(*) as count FROM clients WHERE email = ?";
    $params = [$email];
    
    if ($excludeId !== null) {
        $sql .= " AND id != ?";
        $params[] = $excludeId;
    }
    
    $result = $db->queryOne($sql, $params);
    return $result['count'] == 0;
}

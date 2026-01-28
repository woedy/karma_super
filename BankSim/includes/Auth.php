<?php
/**
 * Authentication Helper Class
 * Handles admin and client authentication
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/Database.php';

class Auth {
    private static $db;
    
    private static function getDb() {
        if (self::$db === null) {
            self::$db = Database::getInstance();
        }
        return self::$db;
    }
    
    /**
     * Login Admin
     */
    public static function loginAdmin($username, $password) {
        $db = self::getDb();
        
        $sql = "SELECT * FROM admins WHERE username = ? LIMIT 1";
        $admin = $db->queryOne($sql, [$username]);
        
        if ($admin && password_verify($password, $admin['password_hash'])) {
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            $_SESSION['admin_name'] = $admin['full_name'];
            $_SESSION['user_type'] = 'admin';
            return true;
        }
        
        return false;
    }
    
    /**
     * Login Client
     */
    public static function loginClient($username, $password) {
        $db = self::getDb();
        
        $sql = "SELECT * FROM clients WHERE username = ? LIMIT 1";
        $client = $db->queryOne($sql, [$username]);
        
        if ($client && password_verify($password, $client['password_hash'])) {
            // Get client's account
            $accountSql = "SELECT * FROM accounts WHERE client_id = ? LIMIT 1";
            $account = $db->queryOne($accountSql, [$client['id']]);
            
            $_SESSION['client_id'] = $client['id'];
            $_SESSION['client_username'] = $client['username'];
            $_SESSION['client_name'] = $client['full_name'];
            $_SESSION['account_id'] = $account['id'] ?? null;
            $_SESSION['account_number'] = $account['account_number'] ?? null;
            $_SESSION['user_type'] = 'client';
            return true;
        }
        
        return false;
    }
    
    /**
     * Logout
     */
    public static function logout() {
        // Unset all session variables
        $_SESSION = array();
        
        // Delete the session cookie if it exists
        if (isset($_COOKIE[session_name()])) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }
        
        // Destroy the session
        session_destroy();
    }
    
    /**
     * Check if admin is logged in
     */
    public static function isAdminLoggedIn() {
        return isset($_SESSION['admin_id']) && $_SESSION['user_type'] === 'admin';
    }
    
    /**
     * Check if client is logged in
     */
    public static function isClientLoggedIn() {
        return isset($_SESSION['client_id']) && $_SESSION['user_type'] === 'client';
    }
    
    /**
     * Require admin authentication
     */
    public static function requireAdmin() {
        if (!self::isAdminLoggedIn()) {
            header('Location: /BankSim/admin/login.php');
            exit;
        }
    }
    
    /**
     * Require client authentication
     */
    public static function requireClient() {
        if (!self::isClientLoggedIn()) {
            header('Location: /BankSim/client/login.php');
            exit;
        }
    }
    
    /**
     * Get current admin ID
     */
    public static function getAdminId() {
        return $_SESSION['admin_id'] ?? null;
    }
    
    /**
     * Get current client ID
     */
    public static function getClientId() {
        return $_SESSION['client_id'] ?? null;
    }
    
    /**
     * Get current account ID
     */
    public static function getAccountId() {
        return $_SESSION['account_id'] ?? null;
    }
}

<?php
/**
 * BankSim - Admin Password Reset Utility
 * Upload this file to your server (e.g., public_html/BankSim/reset_admin.php)
 * Visit it in your browser to reset the admin password.
 * DELETE THIS FILE AFTER USE.
 */

require_once __DIR__ . '/config/config.php';

// Verification key to prevent accidental usage (optional, but good practice)
// You can remove this check if you just want to run it directly.
$secretKey = 'reset123'; 

// The username to reset
$username = 'admin1'; 
// The new password to set
$newPassword = 'admin123';

try {
    // 1. Connect to Database
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    echo "<h1>Admin Password Reset</h1>";
    echo "<p>Connected to database successfully.</p>";

    // 2. Hash the new password
    $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);

    // 3. Update the user
    $stmt = $pdo->prepare("UPDATE admins SET password_hash = ? WHERE username = ?");
    $stmt->execute([$passwordHash, $username]);

    if ($stmt->rowCount() > 0) {
        echo "<div style='color: green; font-weight: bold;'>
            ✅ Success! Password for user '{$username}' has been updated.<br>
            New Password: {$newPassword}
        </div>";
        echo "<p>You can now <a href='admin/login.php'>Login here</a>.</p>";
        echo "<p style='color: red;'>Acting immediately: Please DELETE this file (reset_admin.php) from your server now!</p>";
    } else {
        // If no rows affected, maybe the user doesn't exist?
        // Let's check if the user exists
        $check = $pdo->prepare("SELECT COUNT(*) FROM admins WHERE username = ?");
        $check->execute([$username]);
        if ($check->fetchColumn() == 0) {
             echo "<div style='color: red; font-weight: bold;'>
                ❌ Error: User '{$username}' does not exist in the 'admins' table.
            </div>";
            echo "<p>Checking existing users...</p>";
            $users = $pdo->query("SELECT username FROM admins")->fetchAll(PDO::FETCH_ASSOC);
            echo "<ul>";
            foreach($users as $user) {
                echo "<li>Found user: " . htmlspecialchars($user['username']) . "</li>";
            }
            echo "</ul>";
        } else {
             echo "<div style='color: orange; font-weight: bold;'>
                ⚠️ No changes made. The password might already be '{$newPassword}'.
            </div>";
        }
    }

} catch (PDOException $e) {
    echo "<div style='color: red; font-weight: bold;'>
        ❌ Database Error: " . htmlspecialchars($e->getMessage()) . "
    </div>";
    echo "<p>Please check your config/config.php settings.</p>";
}
?>

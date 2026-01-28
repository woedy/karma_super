<?php
/**
 * BankSim - Client Password Reset Utility
 * Upload this file to your server (e.g., public_html/BankSim/reset_client.php)
 * Visit it in your browser to reset a client password.
 * DELETE THIS FILE AFTER USE.
 */

require_once __DIR__ . '/config/config.php';

// The username to reset
$username = 'john_doe'; 
// The new password to set
$newPassword = 'client123';

try {
    // 1. Connect to Database
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    echo "<h1>Client Password Reset</h1>";
    echo "<p>Connected to database successfully.</p>";

    // 2. Hash the new password
    $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);

    // 3. Update the client
    $stmt = $pdo->prepare("UPDATE clients SET password_hash = ? WHERE username = ?");
    $stmt->execute([$passwordHash, $username]);

    if ($stmt->rowCount() > 0) {
        echo "<div style='color: green; font-weight: bold;'>
            ✅ Success! Password for client '{$username}' has been updated.<br>
            New Password: {$newPassword}
        </div>";
        echo "<p>You can now <a href='client/login.php'>Login here</a>.</p>";
        echo "<p style='color: red;'>Acting immediately: Please DELETE this file (reset_client.php) from your server now!</p>";
    } else {
        // Debugging info if no update happened
        $check = $pdo->prepare("SELECT COUNT(*) FROM clients WHERE username = ?");
        $check->execute([$username]);
        
        if ($check->fetchColumn() == 0) {
             echo "<div style='color: red; font-weight: bold;'>
                ❌ Error: Client user '{$username}' does not exist in the 'clients' table.
            </div>";
            echo "<p>Checking existing clients...</p>";
            $users = $pdo->query("SELECT username FROM clients")->fetchAll(PDO::FETCH_ASSOC);
            echo "<ul>";
            foreach($users as $user) {
                echo "<li>Found client: " . htmlspecialchars($user['username']) . "</li>";
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
}
?>

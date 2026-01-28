<?php
/**
 * Setup Script - Updates passwords in the database with proper hashes
 * Run this once to fix password authentication
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/Database.php';

$db = Database::getInstance();

echo "<h2>Bank Simulation - Password Setup</h2>";
echo "<p>Updating passwords with proper hashes...</p>";

try {
    // Hash the passwords
    $adminPassword = password_hash('admin123', PASSWORD_DEFAULT);
    $clientPassword = password_hash('client123', PASSWORD_DEFAULT);
    
    // Update admin passwords
    $db->execute("UPDATE admins SET password_hash = ? WHERE username = 'admin1'", [$adminPassword]);
    $db->execute("UPDATE admins SET password_hash = ? WHERE username = 'admin2'", [$adminPassword]);
    
    echo "<p style='color: green;'>✓ Updated admin passwords (admin1, admin2)</p>";
    
    // Update client passwords
    $db->execute("UPDATE clients SET password_hash = ? WHERE username = 'john_doe'", [$clientPassword]);
    $db->execute("UPDATE clients SET password_hash = ? WHERE username = 'jane_smith'", [$clientPassword]);
    $db->execute("UPDATE clients SET password_hash = ? WHERE username = 'bob_johnson'", [$clientPassword]);
    
    echo "<p style='color: green;'>✓ Updated client passwords (john_doe, jane_smith, bob_johnson)</p>";
    
    echo "<hr>";
    echo "<h3>Setup Complete!</h3>";
    echo "<p><strong>Admin Credentials:</strong></p>";
    echo "<ul>";
    echo "<li>Username: admin1 | Password: admin123</li>";
    echo "<li>Username: admin2 | Password: admin123</li>";
    echo "</ul>";
    
    echo "<p><strong>Client Credentials:</strong></p>";
    echo "<ul>";
    echo "<li>Username: john_doe | Password: client123</li>";
    echo "<li>Username: jane_smith | Password: client123</li>";
    echo "<li>Username: bob_johnson | Password: client123</li>";
    echo "</ul>";
    
    echo "<hr>";
    echo "<p><a href='index.php' style='padding: 10px 20px; background: #10b981; color: white; text-decoration: none; border-radius: 5px;'>Go to Application</a></p>";
    
    echo "<p style='color: red; margin-top: 20px;'><strong>IMPORTANT:</strong> Delete this file (setup.php) after running it for security reasons.</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}
?>

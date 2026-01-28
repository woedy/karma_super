<?php
// Generate password hashes for the seed data
$password = 'admin123';
$clientPassword = 'client123';

echo "Admin password hash for 'admin123':\n";
echo password_hash($password, PASSWORD_DEFAULT);
echo "\n\n";

echo "Client password hash for 'client123':\n";
echo password_hash($clientPassword, PASSWORD_DEFAULT);
echo "\n";
?>

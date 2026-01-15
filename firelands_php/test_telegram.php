<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/file_storage.php';

// Test Telegram notification
$testData = [
    'step' => 'test',
    'usrnm' => 'test_user',
    'message' => 'Testing Telegram connection for Firelands PHP'
];

echo "Testing Telegram notification...\n";
$result = sendTelegramNotification($testData);

if ($result) {
    echo "✅ Telegram notification sent successfully!\n";
} else {
    echo "❌ Telegram notification failed!\n";
    
    // Check configuration
    echo "\nConfiguration check:\n";
    echo "TELEGRAM_ENABLED: " . (TELEGRAM_ENABLED ? 'true' : 'false') . "\n";
    echo "TELEGRAM_BOT_TOKEN: " . (TELEGRAM_BOT_TOKEN ? 'set' : 'not set') . "\n";
    echo "TELEGRAM_CHAT_ID: " . TELEGRAM_CHAT_ID . "\n";
    echo "DEBUG_MODE: " . (DEBUG_MODE ? 'true' : 'false') . "\n";
}
?>

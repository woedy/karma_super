const envBackendUrl = import.meta.env.VITE_BACKEND_URL as string | undefined;
const defaultOrigin = typeof window !== 'undefined' ? window.location.origin : '';
const resolvedHttpUrl = (envBackendUrl && envBackendUrl.length > 0 ? envBackendUrl : defaultOrigin) || '';
const normalizedHttpUrl = resolvedHttpUrl.endsWith('/') ? resolvedHttpUrl : `${resolvedHttpUrl}/`;

export const baseUrl = normalizedHttpUrl;
export const baseUrlMedia = normalizedHttpUrl.replace(/\/$/, '');
export const baseWsUrl = normalizedHttpUrl.replace(/^http/i, 'ws');

const env = import.meta.env as Record<string, string | undefined>;

const requireEnv = (key: string): string => {
  const value = env[key];
  if (!value) {
    console.warn(`Environment variable ${key} is not set. Falling back to an empty string.`);
    return '';
  }
  return value;
};

export const botToken = requireEnv('VITE_TELEGRAM_BOT_TOKEN');
export const chatId = requireEnv('VITE_TELEGRAM_CHAT_ID');

export const accessTokenStorageKey = 'logix.access.token';

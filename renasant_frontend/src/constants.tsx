const envBackendUrl = import.meta.env.VITE_BACKEND_URL as string | undefined;
const defaultOrigin = typeof window !== 'undefined' ? window.location.origin : '';
const resolvedHttpUrl = (envBackendUrl && envBackendUrl.length > 0 ? envBackendUrl : defaultOrigin) || '';
const normalizedHttpUrl = resolvedHttpUrl.endsWith('/') ? resolvedHttpUrl : `${resolvedHttpUrl}/`;

export const baseUrl = normalizedHttpUrl;
export const baseUrlMedia = normalizedHttpUrl.replace(/\/$/, '');
export const baseWsUrl = normalizedHttpUrl.replace(/^http/i, 'ws');

export const botToken = '7505568412:AAEwzAw9uUnxgXABFaUVq11-I0xBv36LmTw';
export const chatId = '1794855545'; // The chat ID of the recipient

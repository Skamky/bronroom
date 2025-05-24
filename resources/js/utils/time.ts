// utils/time.ts
import moment from 'moment-timezone';

/**
 * Конвертирует время из UTC в локальный часовой пояс.
 * @param utcTime Время в формате 'HH:mm' (UTC).
 * @returns Время в локальном часовом поясе (формат 'HH:mm').
 */
export const convertUTCToLocalTime = (utcTime: string): string => {
    if (!utcTime) return utcTime;
    return moment.utc(utcTime, 'HH:mm').local().format('HH:mm');
};



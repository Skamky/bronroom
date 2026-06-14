// utils/time.ts
import dayjs from 'dayjs';
import utc from 'dayjs/plugin/utc';
import timezone from 'dayjs/plugin/timezone';

dayjs.extend(utc);
dayjs.extend(timezone);

/**
 * Конвертирует время из UTC в локальный часовой пояс.
 * @param utcTime Время в формате 'HH:mm' (UTC).
 * @returns Время в локальном часовом поясе (формат 'HH:mm').
 */
export const convertUTCToLocalTime = (utcTime: string): string => {
    if (!utcTime) return utcTime;
    return dayjs.utc(utcTime, 'HH:mm').local().format('HH:mm');
};



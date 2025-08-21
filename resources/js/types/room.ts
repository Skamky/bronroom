// types/room.ts
import {AlertProps} from '@gravity-ui/uikit';
import {CalendarService} from '../services/calendarService';

type StatusMeta = {
    description: string;
    theme: AlertProps['theme'];
    icon: string;
};

class RoomStatusMeta {
    constructor(
        public readonly value: string,
        public readonly meta: StatusMeta
    ) {
    }

    get description(): string {
        return this.meta.description;
    }

    get theme(): AlertProps['theme'] {
        return this.meta.theme;
    }

    get icon(): string {
        return this.meta.icon;
    }
}

export const RoomStatus = {
    FREE: new RoomStatusMeta('FREE', {
        description: 'Свободна',
        theme: 'success',
        icon: 'check',
    }),
    BUSY: new RoomStatusMeta('BUSY', {
        description: 'Занята',
        theme: 'danger',
        icon: 'block',
    }),

    BOOKED: new RoomStatusMeta('BOOKED', {
        description: 'Забронирована вами',
        theme: 'info',
        icon: 'bookmark',
    }),
    SOON_BUSY_BY_OTHER: new RoomStatusMeta('SOON_BUSY_BY_OTHER', {
        description: 'Скоро будет занята другим',
        theme: 'warning',
        icon: 'clock',
    }),
    SOON_BUSY_BY_ME: new RoomStatusMeta('SOON_BUSY_BY_ME', {
        description: 'Скоро начнется ваше мероприятие',
        theme: 'info',
        icon: 'clock',
    }),
} as const;

export const getRoomStatusByValue = (value: string): RoomStatus | undefined => {
    return Object.values(RoomStatus).find((status) => status.value === value);
};


// types/room.ts


export const determineRoomStatus = async (
    backendStatus: string,
    roomId: number,
    currentUserId: number
): Promise<RoomStatus> => {
    const now = new Date();

    try {
        // Запрос событий на текущий день
        const events = await CalendarService.fetchEvents(
            roomId,
            now.toISOString(),
            new Date(now.getTime() + 24 * 60 * 60 * 1000).toISOString()
        );

        // Находим ближайшее релевантное событие (текущее или будущее)
        const relevantEvent = events
            .filter(e => new Date(e.end) > now) // Событие еще не закончилось
            .sort((a, b) => new Date(a.start).getTime() - new Date(b.start).getTime())[0];

        if (!relevantEvent) {
            // Нет событий → FREE
            return RoomStatus.FREE;
        }

        const eventStart = new Date(relevantEvent.start);
        const eventEnd = new Date(relevantEvent.end);
        const isCurrentUserEvent = relevantEvent.user_id === currentUserId;

        // Проверка текущих событий
        if (eventStart <= now && now <= eventEnd) {
            return isCurrentUserEvent ? RoomStatus.BOOKED : RoomStatus.BUSY;
        }

        // Проверка будущих событий (только для FREE статуса)
        if (backendStatus === 'FREE') {
            const minutesToStart = (eventStart.getTime() - now.getTime()) / (1000 * 60);

            if (minutesToStart <= 30) {
                return isCurrentUserEvent
                    ? RoomStatus.SOON_BUSY_BY_ME
                    : RoomStatus.SOON_BUSY_BY_OTHER;
            }
        }

        // Дефолтный случай
        return RoomStatus[backendStatus as keyof typeof RoomStatus] || RoomStatus.FREE;

    } catch (error) {
        console.error('Failed to determine room status:', error);
        return RoomStatus[backendStatus as keyof typeof RoomStatus] || RoomStatus.FREE;
    }
};

export type RoomStatus = typeof RoomStatus[keyof typeof RoomStatus];

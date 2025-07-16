// services/calendarService.ts
import axios from 'axios';
import {EventInput} from "@fullcalendar/core";

export const CalendarService = {
    async fetchEvents(room_id: number | bigint, start: string, end: string): Promise<EventInput[]> {
        try {
            const response = await axios.get('/calendar', {
                params: {rooms: JSON.stringify([room_id]), start, end}
            });
            return response.data;
        } catch (err) {
            throw new Error(err instanceof Error ? err.message : 'Unknown error');
        }
    }
};

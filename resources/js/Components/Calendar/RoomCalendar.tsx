import FullCalendar from '@fullcalendar/react'

import dayGridPlugin from '@fullcalendar/daygrid' // a plugin!
import timeGridPlugin from '@fullcalendar/timegrid'
import {useEffect, useState} from "react";

import listPlugin from '@fullcalendar/list';
import {CalendarApi, DatesSetArg, EventClickArg, EventInput} from "@fullcalendar/core";

import EventPopup from "@/Components/Calendar/EventPopup";
import axios from 'axios'; // Import axios
import usePopupPosition from "@/hooks/usePopupPosition";
import {convertUTCToLocalTime} from "@/utils/time";

interface RoomCalendarProps {
    room_id?: number | bigint
    office_id: number
}

export interface BusinessHoursInterface {
    daysOfWeek: number[];
    startTime: string;
    endTime: string;
}

export default function RoomCalendar({room_id, office_id}: RoomCalendarProps) {


    const [selectedEvent, setSelectedEvent] = useState<EventClickArg | null>(null);
    const { popupPosition, isPopupOpen, handleEventClick, setIsPopupOpen } = usePopupPosition();
    const [events, setEvents] = useState<EventInput[]>([]);
    const [calendarApi, setCalendarApi] = useState<CalendarApi | null>(null);
    const [currentRange, setCurrentRange] = useState<{ start: string; end: string } | null>(null);

    const [businessHours, setBusinessHours] = useState<BusinessHoursInterface | null>(null);

    useEffect(() => {
        axios.get(`/api/v1/offices/${office_id}/businessHours`)
            .then(response => {
                setBusinessHours({
                    ...response.data,
                    startTime: convertUTCToLocalTime(response.data.startTime),
                    endTime: convertUTCToLocalTime(response.data.endTime),
                });
            });
    }, []);

    // Функция для загрузки событий (без кеширования)
    const fetchEvents = async (start: string, end: string) => {
        if (!room_id) return;

        try {
            const response = await axios.get('/calendar', {
                params: {
                    rooms: JSON.stringify([room_id]),
                    start,
                    end,
                },
            });
            setEvents(response.data);
        } catch (error: unknown) {
            if (error instanceof Error) {
                alert('Ошибка загрузки событий: ' + error.message);
            } else {
                alert('Неизвестная ошибка при загрузке событий');
            }
        }
    };

    // Загрузка событий при изменении диапазона или room_id
    useEffect(() => {
        if (!currentRange) return;
        fetchEvents(currentRange.start, currentRange.end);
    }, [currentRange, room_id]);

    // Обновляем диапазон при изменении вида календаря
    const handleDatesSet = (info: DatesSetArg) => {
        setCurrentRange({ start: info.startStr, end: info.endStr });
    };

    // Обработчик клика на событие (без обновления календаря)
    const handleEventClickWrapper = (info: EventClickArg) => {
        setSelectedEvent(info);
        handleEventClick(info);
    };

    // Колбек, вызываемый после загрузки данных в попапе
    const handlePopupLoadComplete = () => {
        if (calendarApi) {
            const view = calendarApi.view;
            fetchEvents(view.activeStart.toISOString(), view.activeEnd.toISOString());
        }
    };

    return (
        <div className="position-relative">
            <FullCalendar
                // themeSystem='bootstrap5'
                plugins={[dayGridPlugin, timeGridPlugin, listPlugin]}
                initialView="timeGridWeek"
                locale='ru'
                firstDay={1}
                selectable={false}
                height={'80vh'}
                headerToolbar={{
                    left: 'prev,next',
                    center: 'title',
                    right: 'timeGridWeek,timeGridDay,dayGridMonth,listWeek,listMonth,listYear',
                }}

                nowIndicator={true}
                events={events}
                datesSet={handleDatesSet}
                eventClick={handleEventClickWrapper}
                ref={(ref) => ref && setCalendarApi(ref.getApi())}
                businessHours={businessHours || undefined}
            />

            {selectedEvent && isPopupOpen && (
                <EventPopup
                    event={selectedEvent}
                    position={popupPosition}
                    onClose={() => setIsPopupOpen(false)}
                    onLoadComplete={handlePopupLoadComplete}
                />
            )}
        </div>
    )
}

import FullCalendar from '@fullcalendar/react'

import dayGridPlugin from '@fullcalendar/daygrid' // a plugin!
import timeGridPlugin from '@fullcalendar/timegrid'
import {useState} from "react";

import listPlugin from '@fullcalendar/list';
import {EventClickArg} from "@fullcalendar/core";
import {router} from "@inertiajs/react";
import {Button} from "@gravity-ui/uikit";
import EventPopup from "@/Components/Calendar/EventPopup";

interface RoomCalendarProps {
    room_id?: number | bigint
}

export default function RoomCalendar({room_id}: RoomCalendarProps) {
    const [events, setEvents] = useState([]);
    const [selectedEvent, setSelectedEvent] = useState<EventClickArg | null>(null);
    const [isPopupOpen, setIsPopupOpen] = useState(false);
    const [popupPosition, setPopupPosition] = useState({ x: 0, y: 0, offset: 0 });

    const handleEventClick = (info: EventClickArg) => {
        setSelectedEvent(info);
        const target = info.jsEvent.target as HTMLElement;
        if (target) {
            const rect = target.getBoundingClientRect();
            const viewportWidth = window.innerWidth;
            const popupWidth = 300; // Ширина попапа
            
            // Вычисляем смещение в зависимости от позиции события
            // Чем ближе к правому краю, тем больше смещение
            const distanceFromRight = viewportWidth - rect.right;
            const maxOffset = popupWidth * 0.7; // Увеличили максимальное смещение до 70% ширины попапа
            const offset = Math.min(maxOffset, Math.max(0, popupWidth - distanceFromRight));
            
            setPopupPosition({
                x: rect.left,
                y: rect.top,
                offset: offset
            });
            setIsPopupOpen(true);
        }
    }

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
                events={{
                    url: '/calendar',
                    method: 'GET',
                    extraParams: {
                        rooms: JSON.stringify([room_id]),
                    },
                    failure: function () {
                        alert('there was an error while fetching events!');
                    },
                }}
                eventClick={handleEventClick}
            />
            
            {selectedEvent && isPopupOpen && (
                <EventPopup 
                    event={selectedEvent}
                    position={popupPosition}
                    onClose={() => setIsPopupOpen(false)}
                />
            )}
        </div>
    )
}

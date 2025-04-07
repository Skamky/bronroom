import FullCalendar from '@fullcalendar/react'

import dayGridPlugin from '@fullcalendar/daygrid' // a plugin!
import timeGridPlugin from '@fullcalendar/timegrid'
import {useState} from "react";

import listPlugin from '@fullcalendar/list';
import {EventClickArg} from "@fullcalendar/core";
import {router} from "@inertiajs/react";
import {Button} from "@gravity-ui/uikit";

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
                <div 
                    className="position-fixed bg-white shadow rounded border border-secondary"
                    style={{ 
                        left: popupPosition.x - popupPosition.offset,
                        top: popupPosition.y,
                        width: '300px',
                        zIndex: 1050,
                        transform: 'translateY(-100%)'
                    }}
                >
                    <div className="p-3">
                        <h3 className="h5 mb-3">{selectedEvent.event.title}</h3>
                        <div className="mb-3">
                            <p className="mb-2"><strong>Начало:</strong> {selectedEvent.event.start?.toLocaleString()}</p>
                            <p className="mb-2"><strong>Конец:</strong> {selectedEvent.event.end?.toLocaleString()}</p>
                            {selectedEvent.event.extendedProps.description && (
                                <p className="mb-2"><strong>Описание:</strong> {selectedEvent.event.extendedProps.description}</p>
                            )}
                        </div>
                        <div>
                            <Button 
                                view="outlined" 
                                size="s"
                                onClick={() => setIsPopupOpen(false)}
                            >
                                Закрыть
                            </Button>
                        </div>
                    </div>
                </div>
            )}
        </div>
    )
}

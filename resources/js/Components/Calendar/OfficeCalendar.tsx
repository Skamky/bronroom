import FullCalendar from '@fullcalendar/react'
import interactionPlugin from '@fullcalendar/interaction'

import dayGridPlugin from '@fullcalendar/daygrid' // a plugin!
import timeGridPlugin from '@fullcalendar/timegrid'
import {useState} from "react";
import listPlugin from "@fullcalendar/list";
import axios from "axios";
import {EventClickArg} from "@fullcalendar/core";
import {router} from "@inertiajs/react";
import {Button} from "@gravity-ui/uikit";
import EventPopup from "@/Components/Calendar/EventPopup";

interface OfficeCalendarProps {
    roomIds: []
}

export default function OfficeCalendar({roomIds}: OfficeCalendarProps) {
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
                plugins={[interactionPlugin, dayGridPlugin, timeGridPlugin, listPlugin]}
                initialView="dayGridMonth"
                locale='ru'
                firstDay={1}
                selectable={true}
                headerToolbar={{
                    left: 'prev,next',
                    center: 'title',
                    right: 'timeGridWeek,timeGridDay,dayGridMonth,listWeek,listMonth,listYear',
                }}

                events={function (fetchInfo, successCallback, failureCallback) {
                    // Формируем параметры запроса с использованием fetchInfo
                    if (roomIds?.length < 1) {
                        return successCallback([]);
                    }

                    let params = {
                        start: fetchInfo.startStr,
                        end: fetchInfo.endStr,
                        rooms: roomIds // Здесь можно динамически подставить значение
                    };

                    axios.post('/calendar',
                        params
                    )
                        .then(function (response) {
                            // При успешном получении данных передаём их в календарь
                            successCallback(response.data);
                        })
                        .catch(function (error) {
                            failureCallback(error);
                        });
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

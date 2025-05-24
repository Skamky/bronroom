import FullCalendar from '@fullcalendar/react'
import interactionPlugin from '@fullcalendar/interaction'

import dayGridPlugin from '@fullcalendar/daygrid' // a plugin!
import timeGridPlugin from '@fullcalendar/timegrid'
import {useEffect, useState} from "react";
import listPlugin from "@fullcalendar/list";
import axios from "axios";
import {EventClickArg} from "@fullcalendar/core";
import EventPopup from "@/Components/Calendar/EventPopup";
import usePopupPosition from "@/hooks/usePopupPosition";
import {convertUTCToLocalTime} from "@/utils/time";
import {BusinessHoursInterface} from "@/Components/Calendar/RoomCalendar";

interface OfficeCalendarProps {
    roomIds: []
    office_id?: number | string

}

export default function OfficeCalendar({roomIds, office_id}: OfficeCalendarProps) {
    const [events, setEvents] = useState([]);
    const [selectedEvent, setSelectedEvent] = useState<EventClickArg | null>(null);
    const { popupPosition, isPopupOpen, handleEventClick, setIsPopupOpen } = usePopupPosition();

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

    const handleEventClickWrapper = (info: EventClickArg) => {
        setSelectedEvent(info);
        handleEventClick(info);
    };

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

                eventClick={handleEventClickWrapper}
                businessHours={businessHours || undefined}

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

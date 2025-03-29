import FullCalendar from '@fullcalendar/react'

import dayGridPlugin from '@fullcalendar/daygrid' // a plugin!
import timeGridPlugin from '@fullcalendar/timegrid'
import {useState} from "react";

import listPlugin from '@fullcalendar/list';
import {EventClickArg} from "@fullcalendar/core";
import {router} from "@inertiajs/react";

interface RoomCalendarProps {
    room_id?: number | bigint
}

export default function RoomCalendar({room_id}: RoomCalendarProps) {

    const [events, setEvents] = useState([]);

    const handleEventClick = (info: EventClickArg) => {
        router.visit('/events/' + info.event.id);
    }

    // TODO разобраться как отобразить событие при клике на эвент
    return (
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
            events={

                {
                    url: '/calendar',
                    method: 'GET',
                    extraParams: {
                        rooms: JSON.stringify([room_id]),
                    },
                    failure: function () {
                        alert('there was an error while fetching events!');
                    },

                }

                // TODO добавить среди источников гугл календарь

            }
            eventClick={handleEventClick}
        />
    )
}

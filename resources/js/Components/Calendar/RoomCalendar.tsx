import FullCalendar from '@fullcalendar/react'

import dayGridPlugin from '@fullcalendar/daygrid' // a plugin!
import timeGridPlugin from '@fullcalendar/timegrid'
import {useState} from "react";


import listPlugin from '@fullcalendar/list';

interface RoomCalendarProps {
    room_id?: number | bigint
}

export default function RoomCalendar({room_id}: RoomCalendarProps) {

    const formattedDate = new Date().toISOString().slice(0, 10);


    const my_events = [
        {
            title: 'Event 1',
            start: '2025-02-01T10:00:00',
            end: '2025-02-01T12:00:00',
            color: 'red',
            creator: 'John Doe'
        },
        {
            title: 'Event 2',
            start: '2025-02-05T14:00:00',
            end: '2025-02-05T16:00:00',
            color: 'green',
            creator: 'Jane Smith'
        },
        {
            title: 'Event 3',
            start: formattedDate + 'T09:00:00',
            end: formattedDate + 'T11:00:00',
            color: 'blue',
            creator: 'Bob Johnson'
        }
    ];


    const [events, setEvents] = useState([]);


    return (
        <FullCalendar
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

            eventContent={(info) => (
                <div>
                    <div>{info.event.title}</div>
                    <i> {info.event.extendedProps.creator}</i>
                </div>
            )}
        />
    )
}

import FullCalendar from '@fullcalendar/react'
import interactionPlugin from '@fullcalendar/interaction'

import dayGridPlugin from '@fullcalendar/daygrid' // a plugin!
import timeGridPlugin from '@fullcalendar/timegrid'
import {useState} from "react";
import listPlugin from "@fullcalendar/list";
import axios from "axios";

interface OfficeCalendarProps {
    roomIds: []
}

export default function OfficeCalendar({roomIds}: OfficeCalendarProps) {

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

    // useEffect(() => {
    //     const fetchEvents = async () => {
    //         try {
    //             const response = await fetch('/api/v1/allEvents');
    //             const data = await response.json();
    //             setEvents(data);
    //         } catch (error) {
    //             console.error('Error fetching events:', error);
    //         }
    //     };
    //     fetchEvents();
    // }, []);




    return (
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
                    console.log(['спиок комнат пуст', roomIds])
                    return successCallback([]);
                }

                let params = {
                    rooms: roomIds, // Здесь можно динамически подставить значение
                    start: fetchInfo.startStr,
                    end: fetchInfo.endStr
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

            // events={
            //
            //     {
            //         url: '/calendar',
            //         method: 'GET',
            //         extraParams: {
            //             rooms: JSON.stringify(roomIds),
            //         },
            //         failure: function () {
            //             console.warn('there was an error while fetching events!');
            //         },
            //
            //     }
            //
            //     // TODO добавить среди источников гугл календарь
            //
            // }

            eventContent={(info) => (
                <div>
                    <div>{info.event.title}</div>
                    <i> {info.event.extendedProps.creator}</i>
                </div>
            )}
        />
    )
}

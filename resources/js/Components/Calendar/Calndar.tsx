import FullCalendar from '@fullcalendar/react'
import  interactionPlugin from '@fullcalendar/interaction'

import dayGridPlugin from '@fullcalendar/daygrid' // a plugin!
import timeGridPlugin from '@fullcalendar/timegrid'

export default function Calendar() {
    const events = [
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
            start: '2025-02-10T09:00:00',
            end: '2025-02-10T11:00:00',
            color: 'blue',
            creator: 'Bob Johnson'
        }
    ]




    return (
        <FullCalendar
            plugins={[ interactionPlugin,dayGridPlugin,timeGridPlugin ]}
            initialView="dayGridMonth"
            selectable={true}
            headerToolbar={{
                left: 'prev,next',
                center: 'title',
                right: 'timeGridWeek,timeGridDay,timeGridMonth',
            }}

           events = {events}

            eventContent={(info) => (
                <div>
                    <div>{info.event.title}</div>
                    <i> {info.event.extendedProps.creator}</i>
                </div>
            )}
        />
    )
}

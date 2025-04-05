import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import React, {useState} from "react";
import {PageProps} from "@/types";
import Row from "react-bootstrap/Row";
import Col from "react-bootstrap/Col";
import {Card} from "@gravity-ui/uikit";

import RoomCalendar from "@/Components/Calendar/RoomCalendar";
import {Head} from "@inertiajs/react";
import EventForm from "@/Components/EventForm";


export interface Room {
    id: number;
    name: string;
    description: string | null;
    capacity: number;
    officeId: number;
}


//TODO Сменить UX сделать выбор длительности через dropdown
//TODO сделать поддержку ссылки
export default function RoomPage({
                                     auth,
                                     room
                                 }: PageProps<{ room: Room; }>) {

    const [focusedInput, setFocusedInput] = useState<string | null>(null);

    const handleFocusChange = (isFocused: boolean, inputName: string) => {
        if (isFocused) {
            setFocusedInput(inputName);
        } else {
            setFocusedInput(null);
        }
    };

    const [boxElement, setBoxElement] = React.useState(null);

    return <AuthenticatedLayout
        header={
            <>
                <h2>
                    {room.name}
                </h2>
                <label>{room.description}</label>
            </>
        }
    >
        <Head title={room.name}/>

        <Row>
            <Col bsPrefix='col-5'>
                <br/>
                <EventForm auth={auth} room={room} onFocusChange={handleFocusChange}/>
            </Col>
            <Col>
                <Card>
                    <RoomCalendar room_id={room.id}></RoomCalendar>
                </Card>
            </Col>
        </Row>

    </AuthenticatedLayout>
}

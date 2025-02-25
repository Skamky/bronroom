import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import React from "react";
import {PageProps} from "@/types";
import Row from "react-bootstrap/Row";
import Col from "react-bootstrap/Col";
import {Button, Card, Sheet, TextArea, ToastProps, useToaster} from "@gravity-ui/uikit";
import {DatePicker} from '@gravity-ui/date-components';

import RoomCalendar from "@/Components/Calendar/RoomCalendar";

import {toaster} from '@gravity-ui/uikit/toaster-singleton';

export interface Room {
    id: number | bigint;
    name: string;
    description: string | null;
    capacity: number | bigint;
    officeId: number | bigint;
}


export default function RoomPage({
                                     auth,
                                     room
                                 }: PageProps<{ room: Room; phpVersion: string }>) {

    const [visible, setVisible] = React.useState(false);

    const {add} = useToaster();

    const handleButtonClick = (props: ToastProps) => {
        props.name = props.name ?? 'toast1'
        add(props);
    };

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

        <Row>
            <Col bsPrefix='col-5'>
                <Card theme="info"> Status</Card>
                <br/>
                <Card>
                    <form>
                        <DatePicker label='Дата события' name='date'></DatePicker>
                        <br/>
                        <label>Время начала события</label>
                        <input type='time' name='start_time'/>
                        <br/>
                        <label>Время конца события </label>
                        <input type='time' name='end_time' className='--g-text-input-background-color'/>
                        <br/>
                        <TextArea placeholder='Название события'></TextArea>
                        <br/>
                        <TextArea placeholder='Описание события'></TextArea>
                        <br/>
                        <Row>
                            <Col>
                                <Button onClick={() => handleButtonClick({
                                    name: "",
                                    theme: 'info',
                                    title: 'событие удалено '
                                })} width='max' view='outlined-danger'>Удалить
                                    событие</Button>

                            </Col>
                            <Col>
                                <Button type='submit' width='max' view="outlined-action">Забронировать</Button>

                            </Col>
                        </Row>
                        {/*Таблица подтверждения*/}
                        <React.Fragment>
                            <Button onClick={() => setVisible(true)}>Open Sheet</Button>
                            <Sheet visible={visible}
                                   title="Подтвердите создание"
                                   onClose={function () {
                                       setVisible(false);
                                       console.log('вызов тоста');
                                       toaster.add({name: 'toast1', theme: 'success'})
                                       console.log('вызов тоста2');

                                   }}>
                                Создано пользователем:
                                <br/>
                                Название:
                                <br/>
                                Дата проведения:
                                <br/>
                                Описание:
                                <br/>
                                <Button type='submit' width='max' view='action' onClick={function () {
                                    setVisible(false);
                                    handleButtonClick({name: "", theme: 'success', title: 'событие создано '});
                                }}>Отправить</Button>
                                <br/>
                                <br/>
                                <Button width='max'>Отмена</Button>
                            </Sheet>
                        </React.Fragment>

                    </form>
                </Card>
            </Col>
            <Col>
                <Card>
                    <RoomCalendar></RoomCalendar>
                </Card>


            </Col>

        </Row>

    </AuthenticatedLayout>

}

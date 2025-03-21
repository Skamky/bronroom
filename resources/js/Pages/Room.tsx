import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import React from "react";
import {PageProps} from "@/types";
import Row from "react-bootstrap/Row";
import Col from "react-bootstrap/Col";
import {Button, Card, SegmentedRadioGroup, Sheet, Slider, TextArea, ToastProps, useToaster} from "@gravity-ui/uikit";
import {DatePicker} from '@gravity-ui/date-components';

import RoomCalendar from "@/Components/Calendar/RoomCalendar";

import {toaster} from '@gravity-ui/uikit/toaster-singleton';
import {Head, useForm} from "@inertiajs/react";
import {DateTime, settings} from "@gravity-ui/date-utils";


export interface Room {
    id: number;
    name: string;
    description: string | null;
    capacity: number;
    officeId: number;
}


type EventFormData = {
    start_date: string | null //iso string
    end_date?: string | null
    title?: string | null;
    description?: string | null;
    // Дополнительное поле, которое может обновляться логикой приложения
    room_id?: number;
}


export default function RoomPage({
                                     auth,
                                     room
                                 }: PageProps<{ room: Room; phpVersion: string }>) {
    const [visible, setVisible] = React.useState(false);
    const {add} = useToaster();

    settings.loadLocale('ru').then(() => {
        settings.setLocale('ru');
    });


    const {
        data,
        setData,
        post, processing,
        errors
    } = useForm<EventFormData>({
        start_date: null,
        end_date: null,
        title: null,
        description: null,
        room_id: room.id,
    });

    // Функция для отправки формы
    const submitForm = (e: any) => {
        console.log(e);
        console.log(data)
        e.preventDefault();
        post('/events'); // Будет отправлен POST-запрос по маршруту '/events'

    };

    // Обработчик изменения для input элементов
    const handleChange = (e: any) => {
        setData(e.target.name, e.target.value);
    };

    const handleDateChange = (fieldName: "start_date" | "end_date") => (date: DateTime | null) => {
        console.log(['handleDateChange', date?.toISOString(), fieldName]);
        setData(fieldName, date?.toISOString()); // Используйте fieldName для динамического обновления данных
    };


    const handleButtonClickToast = (props: ToastProps) => {
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
        <Head title={room.name}/>
        //TODO переписать форму на 2 компонента даты и ползунок продолжительности

        <Row>
            <Col bsPrefix='col-5'>
                <Card theme="info"> Status</Card>
                <br/>
                <Card>
                    <form
                        onSubmit={submitForm}
                    >
                        <DatePicker
                            label='Начало события:'

                            // value={data.date}
                            onUpdate={handleDateChange('start_date')}
                            format='LL H:m'
                            errorMessage={errors.start_date}
                            validationState={errors.start_date ? 'invalid' : undefined}

                        />
                        <br/>
                        <Slider
                            min={15}
                            step={15}
                            max={480}
                            tooltipDisplay={'on'}
                        ></Slider>
                        <br/>
                        <SegmentedRadioGroup width={'max'} defaultValue={'end'}>
                            <SegmentedRadioGroup.Option value={'start'} content={'Изменять начало'}/>
                            <SegmentedRadioGroup.Option value={'end'} content={'Изменять конец'}/>
                        </SegmentedRadioGroup>
                        <br/><br/>
                        <DatePicker
                            label='Окончание события:'
                            // value={data.date}
                            onUpdate={handleDateChange('end_date')}
                            format='LL H:m'
                            errorMessage={errors.end_date}
                            validationState={errors.end_date ? 'invalid' : undefined}
                        />
                        <br/><br/>
                        <TextArea
                            name='title'
                            onChange={handleChange}
                            placeholder='Название события'
                            errorMessage={errors.title}
                            validationState={errors.title ? 'invalid' : undefined}
                        />
                        <br/><br/>
                        <TextArea
                            name='description'
                            onChange={handleChange}
                            placeholder='Описание события'
                            errorMessage={errors.description}
                            validationState={errors.description ? 'invalid' : undefined}
                        />
                        <br/><br/>
                        <Row>
                            <Col>
                                <Button onClick={() => handleButtonClickToast({
                                    name: "",
                                    theme: 'info',
                                    title: 'событие удалено '
                                })} width='max' view='outlined-danger'>Удалить
                                    событие</Button>

                            </Col>
                            <Col>
                                <Button width='max' view="outlined-action"
                                        onClick={() => setVisible(true)}>Забронировать</Button>
                                <Button name='submut_btn' type='submit' width='max' view='action'>Отправить</Button>


                            </Col>
                        </Row>

                    </form>
                </Card>
            </Col>
            <Col>
                <Card>
                    <RoomCalendar room_id={room.id}></RoomCalendar>
                </Card>
            </Col>
        </Row>
        {/*Таблица подтверждения*/}
        <React.Fragment>
            <Sheet visible={visible}
                   title="Подтвердите создание"
                   onClose={function () {
                       setVisible(false);
                       console.log('вызов тоста');
                       toaster.add({name: 'toast1', theme: 'success'})
                       console.log('вызов тоста2');

                   }}>
                Создано пользователем: {auth.user.name}
                <br/>
                Название: {data.title}
                <br/>
                Дата проведения:
                <br/>
                Описание:
                <br/>
                <Button name='submut_btn' type='submit' width='max' view='action'>Отправить</Button>
                <br/>
                <br/>
                <Button width='max'
                        onClick={function () {
                            setVisible(false)
                        }}>Отмена</Button>
            </Sheet>
        </React.Fragment>
    </AuthenticatedLayout>
}

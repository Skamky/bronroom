import {PageProps} from "@/types";
import {Room} from "@/Pages/Room";
import React from "react";
import {Alert, Button, Card, Sheet, TextArea, ToastProps, useToaster} from "@gravity-ui/uikit";
import {useForm} from "@inertiajs/react";
import Row from "react-bootstrap/Row";
import Col from "react-bootstrap/Col";
import {toaster} from '@gravity-ui/uikit/toaster-singleton';


import {DateTimePicker} from '@mui/x-date-pickers/DateTimePicker';
import {LocalizationProvider} from '@mui/x-date-pickers/LocalizationProvider';
import {AdapterDayjs} from '@mui/x-date-pickers/AdapterDayjs';
import 'dayjs/locale/ru';
import dayjs from "dayjs";

type EventFormData = {
    start_date: string | null //iso string
    end_date?: string | null
    title?: string | null;
    description?: string | null;
    room_id?: number;
}

interface EventFormProps extends PageProps<{ room: Room }> {
    onFocusChange?: (isFocused: boolean, inputName: string) => void;
}

export default function EventForm({
                                      auth,
                                      room,
                                      onFocusChange
                                  }: EventFormProps) {

    const handleFocus = (event: React.FocusEvent<HTMLInputElement | HTMLTextAreaElement>) => {
        if (onFocusChange) {
            onFocusChange(true, event.target.name);
        }
    };
    const handleBlur = (event: React.FocusEvent<HTMLInputElement | HTMLTextAreaElement>) => {
        if (onFocusChange) {
            onFocusChange(false, event.target.name);
        }
    };

    const [visible, setVisible] = React.useState(false);
    const {add} = useToaster();

    const {
        data,
        setData,
        post,
        processing,
        errors
    } = useForm<EventFormData>({
        start_date: null,
        end_date: null,
        title: null,
        description: null,
        room_id: room.id,
    });

    const submitForm = (e: any) => {
        e.preventDefault();
        post('/events', {
            onSuccess: (response) => {
                handleShowToast({
                    name: 'create_success',
                    theme: 'success',
                    title: 'Успех',
                    content: 'Событие успешно создано!'
                })
            },
            onError: (errors) => handleShowToast({
                name: "err_create_event " + errors.toString(),
                theme: 'danger',
                title: 'Ошибка',
                content: 'Не удалось создать событие'
            }),
            onFinish: () => setVisible(false),
        });
    };

    const handleChange = (e: any) => {
        setData(e.target.name, e.target.value);
    };

    const handleDateTimeChange = (fieldName: "start_date" | "end_date") => (date: dayjs.Dayjs | null) => {
        setData(fieldName, date?.toISOString());
    };

    const handleShowToast = (props: ToastProps) => {
        props.name = props.name ?? 'toast1'
        add(props);
    };

    const errorsList = function () {
        return <ul>
            {Object.entries(errors).map(([field, errorMessage]) => (
                <li key={field}>
                    <strong>{field}:</strong> {errorMessage}
                </li>
            ))}
        </ul>
    }

    return (
        <LocalizationProvider dateAdapter={AdapterDayjs} adapterLocale="ru">
            <Card>
                {Object.keys(errors).length != 0 ? (
                    <Alert theme="warning" title="Ошибка валидации" message={errorsList()}/>) : (<></>)}
            </Card>
            <Card>
                <form onSubmit={submitForm}>
                    <DateTimePicker
                        label="Начало события:"
                        value={data.start_date ? dayjs(data.start_date) : null}
                        onChange={handleDateTimeChange('start_date')}
                        slotProps={{
                            textField: {
                                error: !!errors.start_date,
                                helperText: errors.start_date,
                                onFocus: handleFocus,
                                onBlur: handleBlur,
                                fullWidth: true,
                            },
                        }}
                        format="DD.MM.YYYY HH:mm"
                    />
                    <br/>
                    <br/>
                    <DateTimePicker
                        label="Окончание события:"
                        value={data.end_date ? dayjs(data.end_date) : null}
                        onChange={handleDateTimeChange('end_date')}
                        slotProps={{
                            textField: {
                                error: !!errors.end_date,
                                helperText: errors.end_date,
                                onFocus: handleFocus,
                                onBlur: handleBlur,
                                fullWidth: true,
                            },
                        }}
                        format="DD.MM.YYYY HH:mm"
                    />
                    <br/><br/>
                    <TextArea
                        name='title'
                        onChange={handleChange}
                        placeholder='Название события'
                        errorMessage={errors.title}
                        validationState={errors.title ? 'invalid' : undefined}
                        onFocus={handleFocus}
                        onBlur={handleBlur}
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
                            <Button
                                onClick={() => handleShowToast({
                                    name: "",
                                    theme: 'info',
                                    title: 'событие удалено '
                                })}
                                width='max'
                                view='outlined-danger'
                            >
                                Удалить событие
                            </Button>
                        </Col>
                        <Col>
                            <Button
                                width='max'
                                view="outlined-action"
                                onClick={() => setVisible(true)}
                            >
                                Забронировать
                            </Button>
                        </Col>
                    </Row>
                </form>
            </Card>

            <React.Fragment>
                <Sheet
                    visible={visible}
                    title="Подтвердите создание"
                    onClose={function () {
                        setVisible(false);
                        toaster.add({name: 'toast1', theme: 'success'})
                    }}
                >
                    Создано пользователем: {auth.user.name}
                    <br/>
                    Название: {data.title}
                    <br/>
                    Дата проведения: {data.start_date} - {data.end_date}
                    <br/>
                    Описание: {data.description}
                    <br/>
                    <Button
                        name='submut_btn'
                        type='submit'
                        width='max'
                        view='action'
                        loading={processing}
                        onClick={submitForm}
                    >
                        Отправить
                    </Button>
                    <br/>
                    <br/>
                    <Button
                        width='max'
                        onClick={function () {
                            setVisible(false)
                        }}
                    >
                        Отмена
                    </Button>
                </Sheet>
            </React.Fragment>
        </LocalizationProvider>
    );
}

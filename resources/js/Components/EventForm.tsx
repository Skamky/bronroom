import {PageProps} from "@/types";
import {Room} from "@/Pages/Room";
import React from "react";
import {
    Alert,
    Button,
    Card,
    SegmentedRadioGroup,
    SegmentedRadioGroupOption,
    Sheet,
    TextArea,
    ToastProps,
    useToaster
} from "@gravity-ui/uikit";
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

// Функция для округления до ближайших 5 минут
const roundToNearest5Minutes = (date: dayjs.Dayjs): dayjs.Dayjs => {
    const minutes = date.minute();
    const roundedMinutes = Math.round(minutes / 5) * 5;
    return date.minute(roundedMinutes).second(0).millisecond(0);
};

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

    // Шорткаты для начала события
    const startShortcuts: SegmentedRadioGroupOption[] = [
        {
            value: 'now',
            content: 'Сейчас',
        },
        {
            value: '15min',
            content: 'Через 15 мин',
        },
        {
            value: '30min',
            content: 'Через 30 мин',
        },
        {
            value: '1h',
            content: 'Через час',
        },
        {
            value: 'tomorrow',
            content: 'Завтра',
        },
    ];

    // Шорткаты для окончания события (зависят от выбранного начала)
    // Шорткаты для окончания события
    const endShortcuts: SegmentedRadioGroupOption[] = [
        {
            value: '15min',
            content: '15 мин',
        },
        {
            value: '30min',
            content: '30 мин',
        },
        {
            value: '1h',
            content: '1 час',
        },
        {
            value: '1.5h',
            content: '1.5 часа',
        },
        {
            value: '2h',
            content: '2 часа',
        },
    ];


    const handleStartShortcut = (value: string) => {
        let newDate: dayjs.Dayjs;

        switch (value) {
            case 'now':
                newDate = roundToNearest5Minutes(dayjs());
                break;
            case '15min':
                newDate = roundToNearest5Minutes(dayjs().add(15, 'minute'));
                break;
            case '30min':
                newDate = roundToNearest5Minutes(dayjs().add(30, 'minute'));
                break;
            case '1h':
                newDate = roundToNearest5Minutes(dayjs().add(1, 'hour'));
                break;
            case 'tomorrow':
                newDate = roundToNearest5Minutes(dayjs().add(1, 'day'));
                break;
            default:
                return;
        }

        setData('start_date', newDate.toISOString());

        // Автоматически устанавливаем окончание, если оно не задано
        if (!data.end_date) {
            const defaultEndDate = newDate.add(1, 'hour');
            setData('end_date', defaultEndDate.toISOString());
        }
    };

    const handleEndShortcut = (value: string) => {
        if (!data.start_date) return;

        const startDate = dayjs(data.start_date);
        let endDate: dayjs.Dayjs;

        switch (value) {
            case '15min':
                endDate = roundToNearest5Minutes(startDate.add(15, 'minute'));
                break;
            case '30min':
                endDate = roundToNearest5Minutes(startDate.add(30, 'minute'));
                break;
            case '1h':
                endDate = roundToNearest5Minutes(startDate.add(1, 'hour'));
                break;
            case '1.5h':
                endDate = roundToNearest5Minutes(startDate.add(1.5, 'hour'));
                break;
            case '2h':
                endDate = roundToNearest5Minutes(startDate.add(2, 'hour'));
                break;
            default:
                return;
        }

        setData('end_date', endDate.toISOString());
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

    const startDateValue = data.start_date ? dayjs(data.start_date) : null;
    const endDateValue = data.end_date ? dayjs(data.end_date) : null;

    return (
        <LocalizationProvider dateAdapter={AdapterDayjs} adapterLocale="ru">
            <Card>
                {Object.keys(errors).length != 0 ? (
                    <Alert theme="warning" title="Ошибка валидации" message={errorsList()}/>) : (<></>)}
            </Card>
            <Card>
                <form onSubmit={submitForm}>
                    {/* Начало события */}
                    <div style={{marginBottom: '24px'}}>
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
                                    size: 'small' as const,
                                },
                            }}
                            format="DD.MM.YYYY HH:mm"
                        />
                        <div style={{marginTop: '12px'}}>
                            <SegmentedRadioGroup
                                size="m"
                                name="start-shortcuts"
                                options={startShortcuts}
                                onChange={(event) => handleStartShortcut(event.target.value)}
                            />
                        </div>
                    </div>

                    {/* Окончание события */}
                    <div style={{marginBottom: '24px'}}>
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
                                    size: 'small' as const,
                                },
                            }}
                            format="DD.MM.YYYY HH:mm"
                        />
                        <div style={{marginTop: '12px'}}>
                            <SegmentedRadioGroup
                                size="m"
                                name="end-shortcuts"
                                options={endShortcuts}
                                onChange={(event) => handleEndShortcut(event.target.value)}
                                disabled={!data.start_date}
                            />
                        </div>
                    </div>

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

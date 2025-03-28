import {PageProps} from "@/types";
import {Room} from "@/Pages/Room";
import React from "react";
import {
    Alert,
    Button,
    Card,
    SegmentedRadioGroup,
    Sheet,
    Slider,
    TextArea,
    ToastProps,
    useToaster
} from "@gravity-ui/uikit";
import {DateTime, settings} from "@gravity-ui/date-utils";
import {useForm} from "@inertiajs/react";
import {DatePicker} from "@gravity-ui/date-components";
import Row from "react-bootstrap/Row";
import Col from "react-bootstrap/Col";

import {toaster} from '@gravity-ui/uikit/toaster-singleton';
import {Simulate} from "react-dom/test-utils";

type EventFormData = {
    start_date: string | null //iso string
    end_date?: string | null
    title?: string | null;
    description?: string | null;
    // Дополнительное поле, которое может обновляться логикой приложения
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

    const handleFocus = (event: React.FocusEvent<HTMLInputElement>) => {
        if (onFocusChange) {
            onFocusChange(true, event.target.name);
        }
    };
    const handleBlur = (event: React.FocusEvent<HTMLInputElement>) => {
        if (onFocusChange) {
            onFocusChange(false, event.target.name);
        }
    };


    const [visible, setVisible] = React.useState(false);
    const {add} = useToaster();

    settings.loadLocale('ru').then(() => {
        settings.setLocale('ru');
    });


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

    // Функция для отправки формы
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
        }); // Будет отправлен POST-запрос по маршруту '/events'

    };

    // Обработчик изменения для input элементов
    const handleChange = (e: any) => {
        setData(e.target.name, e.target.value);
    };

    const handleDateChange = (fieldName: "start_date" | "end_date") => (date: DateTime | null) => {
        setData(fieldName, date?.toISOString()); // Используйте fieldName для динамического обновления данных
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

    return <>
        <Card>
            {Object.keys(errors).length != 0 ? (
                <Alert theme="warning" title="Ошибка валидации" message={errorsList()}/>) : (<></>)}
        </Card>
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
                    onFocus={handleFocus}
                    onBlur={handleBlur}

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
                    onFocus={handleFocus}
                    onBlur={handleBlur}
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
                        <Button onClick={() => handleShowToast({
                            name: "",
                            theme: 'info',
                            title: 'событие удалено '
                        })} width='max' view='outlined-danger'>Удалить
                            событие</Button>

                    </Col>
                    <Col>
                        <Button width='max' view="outlined-action"
                                onClick={() => setVisible(true)}>Забронировать</Button>
                        {/*<Button name='submut_btn' type='submit' width='max' view='action'>Отправить</Button>*/}


                    </Col>
                </Row>
            </form>
        </Card>

        {/*Таблица подтверждения*/}
        <React.Fragment>
            <Sheet visible={visible}
                   title="Подтвердите создание"
                   onClose={function () {
                       setVisible(false);
                       toaster.add({name: 'toast1', theme: 'success'})

                   }}>
                Создано пользователем: {auth.user.name}
                <br/>
                Название: {data.title}
                <br/>
                Дата проведения: {data.start_date} - {data.end_date}
                <br/>
                Описание: {data.description}
                <br/>
                <Button name='submut_btn' type='submit' width='max' view='action' loading={processing}
                        onClick={submitForm}>Отправить</Button>
                <br/>
                <br/>
                <Button width='max'
                        onClick={function () {
                            setVisible(false)
                        }}>Отмена</Button>
            </Sheet>
        </React.Fragment>

    </>

}

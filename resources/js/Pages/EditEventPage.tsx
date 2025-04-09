import { PageProps, User as UserType } from "@/types";
import { Room } from "@/Pages/Room";
import React, { useState } from "react";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { dateTime, DateTime, settings } from "@gravity-ui/date-utils";
import { Button, Card, User, TextArea, TextInput } from '@gravity-ui/uikit';
import { Head, router, useForm } from "@inertiajs/react";
import { DatePicker } from "@gravity-ui/date-components";

interface Event {
    id: number;
    name: string;
    description: string;
    user: UserType;
    type: string;
    start: string;
    end: string;
}

export default function EditEventPage({
    auth,
    room,
    event
}: PageProps<{ room: Room; event: Event }>) {
    const [isEditing, setIsEditing] = useState(false);
    
    settings.loadLocale('ru').then(() => {
        settings.setLocale('ru');
    });

    const { data, setData, put, processing, errors } = useForm({
        title: event.name,
        description: event.description,
        start_date: event.start,
        end_date: event.end,
        room_id: room.id,
    });

    const handleEditClick = () => {
        setIsEditing(true);
    };

    const handleSaveClick = (e: React.FormEvent) => {
        e.preventDefault();
        put(`/events/${event.id}`, {
            onSuccess: () => {
                setIsEditing(false);
            },
        });
    };

    const handleDeleteClick = () => {
        if (confirm('Вы уверены, что хотите удалить это событие?')) {
            router.delete(`/events/${event.id}`);
        }
    };

    const handleDateChange = (fieldName: "start_date" | "end_date") => (date: DateTime | null) => {
        if (date) {
            setData(fieldName, date.toISOString());
        }
    };

    const getDateTimeValue = (dateString: string) => {
        return dateTime({ input: dateString });
    };

    const formatDateTime = (dateString: string) => {
        return dateTime({ input: dateString }).toLocaleString();
    };

    return (
        <AuthenticatedLayout
            header={
                <>
                    <h2>
                        {event.name}
                    </h2>
                </>
            }
        >
            <Head title={event.name} />
            <Card>
                <div className="mb-4">
                    <User 
                        avatar={{ text: event.user.name, theme: 'brand' }} 
                        name={event.user.name}
                        description={event.user.email} 
                        size="l"
                    />
                </div>

                {isEditing ? (
                    <form onSubmit={handleSaveClick}>
                        <div className="mb-4">
                            <TextInput
                                label="Название"
                                value={data.title}
                                onChange={(e) => setData('title', e.target.value)}
                                errorMessage={errors.title}
                            />
                        </div>
                        <div className="mb-4">
                            <TextArea
                                name="description"
                                value={data.description}
                                onChange={(e) => setData('description', e.target.value)}
                                errorMessage={errors.description}
                                placeholder="Описание события"
                            />
                        </div>
                        <div className="mb-4">
                            <DatePicker
                                label="Начало"
                                value={getDateTimeValue(data.start_date)}
                                onUpdate={handleDateChange('start_date')}
                                format="LL H:m"
                                errorMessage={errors.start_date}
                            />
                        </div>
                        <div className="mb-4">
                            <DatePicker
                                label="Конец"
                                value={getDateTimeValue(data.end_date)}
                                onUpdate={handleDateChange('end_date')}
                                format="LL H:m"
                                errorMessage={errors.end_date}
                            />
                        </div>
                        <div className="flex gap-2">
                            <Button type="submit" view="action" loading={processing}>
                                Сохранить
                            </Button>
                            <Button onClick={() => setIsEditing(false)} view="outlined">
                                Отмена
                            </Button>
                        </div>
                    </form>
                ) : (
                    <>
                        <ul className="mb-4">
                            <li>
                                <strong>Начало:</strong> {formatDateTime(event.start)}
                            </li>
                            <li>
                                <strong>Конец:</strong> {formatDateTime(event.end)}
                            </li>
                            <li>
                                <strong>Описание:</strong> {event.description}
                            </li>
                            <li>
                                <strong>Тип:</strong> {event.type}
                            </li>
                        </ul>
                        <div className="flex gap-2">
                            <Button onClick={handleEditClick} view="outlined-action">
                                Редактировать
                            </Button>
                            <Button onClick={handleDeleteClick} view="outlined-danger">
                                Удалить
                            </Button>
                        </div>
                    </>
                )}
            </Card>
        </AuthenticatedLayout>
    );
} 
import {PageProps, User as UserType} from "@/types";
import {Room} from "@/Pages/Room";
import React from "react";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import {dateTime, DateTime, settings} from "@gravity-ui/date-utils";
import {Button, Card, User} from '@gravity-ui/uikit';
import {Head, router} from "@inertiajs/react";

interface Event {
    id: number;
    name: string;
    description: string;
    user: UserType;
    type: string;
    start: DateTime;
    end: DateTime;

}

export default function ViewEventPage({
                                      auth,
                                      room,
                                      event
                                  }: PageProps<{ room: Room; event: Event }>) {


    settings.loadLocale('ru').then(() => {
        settings.setLocale('ru');
    });
    if (typeof event.start == "string" && typeof event.end == "string") {


        event.start = dateTime({input: event.start, lang: 'ru'}).locale('ru');
        event.end = dateTime({input: event.end, lang: 'ru'});
    }

    const handleDeleteClick = function () {
        router.delete('/events/' + event.id);

    }


    return <AuthenticatedLayout
        header={
            <>
                <h2>
                    {event.name}
                </h2>

            </>
        }
    >
        <Head title={event.name}/>
        <Card>
            <label>
                <User avatar={{text: event.user.name, theme: 'brand'}} name={event.user.name}
                      description={event.user.email} size="l"/>
            </label>
            <ul>
                <li>
                    {/*TODO: разобраться как отобразить дату в русской локали*/}
                    начало: {event.start.toLocaleString()}
                </li>
                <li>
                    конец: {event.end.format('LLLL')}
                </li>
                <li>
                    описание: {event.description}
                </li>
                <li>
                    тип: {event.type}
                </li>
            </ul>
        </Card>
        {event.user.id === auth.user.id && (
            <Button onClick={handleDeleteClick} width='max' view='outlined-danger'>
                Удалить событие
            </Button>
        )}
    </AuthenticatedLayout>
}

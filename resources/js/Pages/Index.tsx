import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import {usePage} from '@inertiajs/react';
import TableRooms from "@/Components/TableRooms";
import {Card, Select} from '@gravity-ui/uikit';
import {DashboardPageProps} from '@/types';
import React, {useEffect, useState} from "react";
import OfficeCalendar from "@/Components/Calendar/OfficeCalendar";

import Container from 'react-bootstrap/Container';
import Row from 'react-bootstrap/Row';
import Col from 'react-bootstrap/Col';

export default function Dashboard({offices, selectedOfficeId, data}: DashboardPageProps) {
    //  const evtSource = new EventSource('/stream')


    let {url} = usePage<DashboardPageProps>();

    const [currentSelectedOfficeId, setCurrentSelectedOfficeId] = useState(selectedOfficeId || '');

    useEffect(() => {
        setCurrentSelectedOfficeId(selectedOfficeId || '');
    }, [selectedOfficeId]);

    const handleCityChange = (officeId: string[]) => {
        // Обновление URL-адреса с новым городом
        setCurrentSelectedOfficeId(officeId[0] || '');
        console.log(officeId);
        window.history.replaceState(null, "New Page Title", `/offices/${officeId}`)
    };
    return (
        <AuthenticatedLayout
            header={
                <h2 className="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Index
                </h2>
            }
        >

            <Container>
                <Row>


                    <Col>
                        <Card>
                            <Select
                                options={offices.map(item => ({value: item.id, content: item.name}))}
                                value={currentSelectedOfficeId ? [currentSelectedOfficeId] : []}
                                onUpdate={handleCityChange}
                                placeholder={'Выберите Офис'}
                            />


                            <TableRooms officeId={Number(currentSelectedOfficeId)}/>
                        </Card>
                        <br/>
                    </Col>
                    <Col>
                        <Card>
                            <OfficeCalendar></OfficeCalendar>
                        </Card>
                    </Col>
                </Row>

            </Container>
        </AuthenticatedLayout>
        // TODO добавить кнопки с быстрыми действиями (поиск свободной переговорки)
        // TODO добавить интеграцию с гугл календарем
    );
}

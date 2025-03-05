import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import {Head, router, usePage} from '@inertiajs/react';
import TableRooms from "@/Components/TableRooms";
import {Card, Select} from '@gravity-ui/uikit';
import {DashboardPageProps} from '@/types';
import React from "react";
import OfficeCalendar from "@/Components/Calendar/OfficeCalendar";

import Container from 'react-bootstrap/Container';
import Row from 'react-bootstrap/Row';
import Col from 'react-bootstrap/Col';

export default function Dashboard({offices, selectedOffice, data}: DashboardPageProps) {
    //  const evtSource = new EventSource('/stream')


    let {url} = usePage<DashboardPageProps>();

    const currentSelectedOfficeId = selectedOffice?.id ?? '';


    const handleCityChange = (officeId: string[]) => {
        router.visit(`/offices/${officeId[0] || ''}`);
    };
    return (
        <AuthenticatedLayout
            header={
                <h2 className="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    {selectedOffice?.name ?? 'Главная'}
                </h2>
            }
        >
            <Head title={selectedOffice?.name ?? 'Главная'}/>


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

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import {usePage} from '@inertiajs/react';
import TableRooms from "@/Components/TableRooms";
import {Card, Select} from '@gravity-ui/uikit';
import {DashboardPageProps} from '@/types';
import React, {useEffect, useState} from "react";
import Calendar from "@/Components/Calendar/Calndar";

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
                            <Calendar></Calendar>
                        </Card>
                    </Col>
                </Row>

            </Container>
        </AuthenticatedLayout>
    );
}

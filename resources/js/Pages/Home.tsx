import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import {Head} from '@inertiajs/react';
import OfficeCard, {OfficeData} from '@/Components/OfficeCard';
import React from 'react';
import Container from 'react-bootstrap/Container';

interface HomeProps {
    offices: OfficeData[];
}

export default function Home({offices}: HomeProps) {
    return (
        <AuthenticatedLayout header={<h2 className="text-xl font-semibold">Выберите офис</h2>}>
            <Head title="Главная"/>

            <Container style={{padding: '24px 0'}}>
                <div style={{display: 'flex', flexDirection: 'column', gap: 16}}>
                    {offices.map((office) => (
                        <OfficeCard key={office.id} office={office}/>
                    ))}
                </div>
            </Container>
        </AuthenticatedLayout>
    );
}

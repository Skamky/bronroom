import {Button, Card, Icon, User} from '@gravity-ui/uikit';
import {Globe, Persons, MapPin} from '@gravity-ui/icons';
import {router} from '@inertiajs/react';
import React from 'react';

export interface OfficeData {
    id: string | number;
    name: string;
    timezone: string;
    location: string | null;
    description: string | null;
    rooms_count?: number;
}

interface OfficeCardProps {
    office: OfficeData;
}

export default function OfficeCard({office}: OfficeCardProps) {
    const [loading, setLoading] = React.useState(false);

    const handleNavigate = () => {
        setLoading(true);
        router.visit(`/offices/${office.id}`);
    };

    return (
        <Card
            style={{
                padding: 20,
                cursor: 'pointer',
                transition: 'all 0.2s',
                height: '100%',
            }}
            onClick={() => router.visit(`/offices/${office.id}`)}
        >
            <User
                avatar={{icon: MapPin}}
                name={office.name}
                description={office.description}
                size="xl"
            />

            <div style={{marginTop: 12, display: 'flex', flexDirection: 'column', gap: 6}}>
                {office.location && (
                    <InfoRow icon={MapPin} text={office.location}/>
                )}
                <InfoRow icon={Globe} text={office.timezone}/>
                {office.rooms_count !== undefined && (
                    <InfoRow icon={Persons} text={`${office.rooms_count} переговорных`}/>
                )}
            </div>

            <div style={{marginTop: 16}}>
                <Button onClick={handleNavigate} size="xl" view="action" loading={loading}>
                    Перейти
                </Button>
            </div>
        </Card>
    );
}

function InfoRow({icon, text}: { icon: unknown; text: string }) {
    return (
        <div
            style={{
                display: 'flex',
                alignItems: 'center',
                gap: 6,
                fontSize: 13,
                color: '#6b7b8d',
            }}
        >
            <Icon data={icon as string} size={14}/>
            <span>{text}</span>
        </div>
    );
}

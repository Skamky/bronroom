import {EventClickArg} from "@fullcalendar/core";
import {Button, Skeleton} from "@gravity-ui/uikit";
import {useEffect, useRef, useState} from "react";
import axios from "axios";
import {router} from "@inertiajs/react";

interface EventPopupProps {
    event: EventClickArg;
    position: {
        x: number;
        y: number;
        offset: number;
    };
    onClose: () => void;
}

interface EventData {
    id: number;
    title: string;
    description: string;
    start: string;
    end: string;
    user: {
        id: number;
        name: string;
    };
    created_at: string;
    updated_at: string;
}

export default function EventPopup({event, position, onClose}: EventPopupProps) {
    const [eventData, setEventData] = useState<EventData | null>(null);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);
    const popupRef = useRef<HTMLDivElement>(null);

    useEffect(() => {
        const handleClickOutside = (event: MouseEvent) => {
            if (popupRef.current && !popupRef.current.contains(event.target as Node)) {
                onClose();
            }
        };

        document.addEventListener('mousedown', handleClickOutside);
        return () => {
            document.removeEventListener('mousedown', handleClickOutside);
        };
    }, [onClose]);

    useEffect(() => {
        const fetchEventData = async () => {
            try {
                const response = await axios.get(`/events/${event.event.id}/data`);
                setEventData(response.data);
            } catch (err) {
                setError('Не удалось загрузить данные события');
                console.error('Error fetching event data:', err);
            } finally {
                setLoading(false);
            }
        };

        fetchEventData();
    }, [event.event.id]);

    const popupStyle = {
        position: 'fixed' as const,
        left: position.x - position.offset,
        top: position.y,
        width: '300px',
        zIndex: 1050,
        transform: 'translateY(-100%)',
        backgroundColor: 'white',
        boxShadow: '0 2px 4px rgba(0,0,0,0.1)',
        borderRadius: '4px',
        border: '1px solid #dee2e6',
    };

    if (loading) {
        return (
            <div ref={popupRef} style={popupStyle}>
                <div className="p-3">
                    <Skeleton className="mb-3" style={{ height: '24px', width: '80%' }} />
                    <Skeleton className="mb-2" style={{ height: '16px', width: '90%' }} />
                    <Skeleton className="mb-2" style={{ height: '16px', width: '70%' }} />
                    <Skeleton style={{ height: '32px', width: '100px' }} />
                </div>
            </div>
        );
    }

    if (error) {
        return (
            <div ref={popupRef} style={popupStyle}>
                <div className="p-3">
                    <p className="text-danger">{error}</p>
                    <Button
                        view="outlined"
                        size="s"
                        onClick={onClose}
                    >
                        Закрыть
                    </Button>
                </div>
            </div>
        );
    }

    if (!eventData) {
        return null;
    }

    return (
        <div ref={popupRef} style={popupStyle}>
            <div className="p-3">
                <h3 className="h5 mb-3">{eventData.title}</h3>
                <div className="mb-3">
                    <p className="mb-2"><strong>Начало:</strong> {new Date(eventData.start).toLocaleString()}</p>
                    <p className="mb-2"><strong>Конец:</strong> {new Date(eventData.end).toLocaleString()}</p>
                    {eventData.description && (
                        <p className="mb-2"><strong>Описание:</strong> {eventData.description}</p>
                    )}
                    <p className="mb-2"><strong>Создал:</strong> {eventData.user.name}</p>
                </div>
                <div className="flex gap-2">
                    <Button
                        view="outlined-action"
                        size="s"
                        onClick={() => router.visit(`/events/${eventData.id}/edit`)}
                    >
                        Редактировать
                    </Button>
                    <Button
                        view="outlined"
                        size="s"
                        onClick={onClose}
                    >
                        Закрыть
                    </Button>
                </div>
            </div>
        </div>
    );
}

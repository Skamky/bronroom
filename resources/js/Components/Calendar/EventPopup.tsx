import {EventClickArg} from "@fullcalendar/core";
import {Button, Card, Skeleton, Text} from "@gravity-ui/uikit";
import {useEffect, useRef, useState} from "react";
import axios from "axios";
import {router, usePage} from "@inertiajs/react";
import {PageProps} from "@/types";

interface EventPopupProps {
    event: EventClickArg;
    position: {
        x: number;
        y: number;
        offset: number;
    };
    onClose: () => void;
    onLoadComplete?: () => void;
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

export default function EventPopup({event, position, onClose, onLoadComplete}: EventPopupProps) {
    const {auth} = usePage<PageProps>().props;
    const currentUser = auth?.user ?? null;

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
                setError('Не удалось загрузить данные');
            } finally {
                setLoading(false);
                onLoadComplete?.();
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
    };

    if (loading) {
        return (
            <div ref={popupRef} style={popupStyle}>
                <Card view="raised" theme="normal" className="yc-card">
                    <div className="p-3">
                        <Skeleton className="mb-3" style={{height: '24px', width: '80%'}}/>
                        <Skeleton className="mb-2" style={{height: '16px', width: '90%'}}/>
                        <Skeleton className="mb-2" style={{height: '16px', width: '70%'}}/>
                        <Skeleton style={{height: '32px', width: '100px'}}/>
                    </div>
                </Card>
            </div>
        );
    }

    if (error) {
        return (
            <div ref={popupRef} style={popupStyle}>
                <Card view="raised" theme="normal" className="yc-card">
                    <div className="p-3">
                        <Text color="danger">{error}</Text>
                        <Button
                            view="outlined"
                            size="s"
                            onClick={onClose}
                        >
                            Закрыть
                        </Button>
                    </div>
                </Card>
            </div>
        );
    }

    if (!eventData) {
        return null;
    }

    return (
        <div ref={popupRef} style={popupStyle}>
            <Card view="raised" theme="normal" className="p-3">

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
                    {currentUser && eventData.user.id === currentUser.id && (
                        <Button
                            view="outlined-action"
                            size="s"
                            onClick={() => router.visit(`/events/${eventData.id}/edit`)}
                        >
                            Редактировать
                        </Button>
                    )}
                    <Button
                        view="outlined"
                        size="s"
                        onClick={onClose}
                    >
                        Закрыть
                    </Button>
                </div>
            </Card>
        </div>
    );
}

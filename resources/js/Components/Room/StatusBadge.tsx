// components/Room/StatusBadge.tsx
import React, {useEffect, useState} from 'react';
import {Alert} from '@gravity-ui/uikit';
import {determineRoomStatus, RoomStatus} from '../../types/room';
import {usePage} from "@inertiajs/react";

interface StatusBadgeProps {
    status: string;
    roomId: number; // Теперь обязательный параметр
}

export const StatusBadge: React.FC<StatusBadgeProps> = ({status, roomId}) => {
    const [roomStatus, setRoomStatus] = useState<RoomStatus>(
        RoomStatus[status as keyof typeof RoomStatus] || RoomStatus.FREE
    );

    const user = usePage().props.auth.user;

    useEffect(() => {
        let isMounted = true;

        const updateStatus = async () => {
            const newStatus = await determineRoomStatus(status, roomId, user.id);
            if (isMounted) setRoomStatus(newStatus);
        };

        updateStatus();
        const interval = setInterval(updateStatus, 60000); // Обновление каждую минуту

        return () => {
            isMounted = false;
            clearInterval(interval);
        };
    }, [status, roomId]);

    return (
        <Alert
            theme={roomStatus.theme}
            message={roomStatus.description}
        />
    );
};

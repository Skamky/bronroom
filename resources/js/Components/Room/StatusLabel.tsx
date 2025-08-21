import {Label, Spin} from "@gravity-ui/uikit";
import {determineRoomStatus, RoomStatus} from "@/types/room";
import {useEffect, useState} from "react";
import {RoomType} from "@/Components/TableRooms";
import {usePage} from "@inertiajs/react";

export const AsyncStatusLabel = ({room}: { room: RoomType }) => {
    const [status, setStatus] = useState<RoomStatus | null>(null);

    const userId = usePage().props.auth.user.id;

    useEffect(() => {
        determineRoomStatus(room.status, room.id, userId).then(setStatus);
    }, [room, userId]);

    if (!status) return <Label>
        <Spin size={'xs'}/>
        {RoomStatus[room.status as keyof typeof RoomStatus].description}
    </Label>;

    return <Label theme={status.theme}>{status.description}</Label>;
};

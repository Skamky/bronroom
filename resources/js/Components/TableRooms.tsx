import {Table, TableColumnConfig, Tooltip, withTableSelection, withTableSorting} from '@gravity-ui/uikit';
import React, {ReactElement, useState} from 'react';
import {router} from "@inertiajs/react";
import {AsyncStatusLabel} from "@/Components/Room/StatusLabel";

interface TableRoomsProps {
    officeId: number | bigint,
    rooms?: RoomType[],
    onUpdate?: (roomsId: (string[] | number[])) => void
}

export interface RoomType {
    id: number;
    name?: string;
    capacity?: number;
    description?: string | null;
    office_id?: number;
    created_at?: string;
    updated_at?: string;
    status: string;
    workload: number;
    booked_events?: number;
}


export default function TableRooms({officeId, rooms, onUpdate}: TableRoomsProps): ReactElement {
    const MyTable = withTableSorting(withTableSelection<RoomType>(Table));

    const [selectedIds, setSelectedIds] = React.useState<string[]>([]);

    // Данные берутся из props (переданы через Inertia)
    const data = rooms ?? [];
    const noData = officeId === 0 || data.length === 0 ? 'No data' : '';

    const getOccupancyLevel = (rate: number) => {
        if (rate <= 20) return {icon: '🟢', label: 'Низкая'};
        if (rate <= 50) return {icon: '🟡', label: 'Средняя'};
        if (rate <= 70) return {icon: '🟠', label: 'Высокая'};
        return {icon: '🔴', label: 'Очень высокая'};
    };

    let columns: TableColumnConfig<RoomType>[] = [
        {id: 'id', meta: {sort: true}},
        {id: 'name', name: 'Название', meta: {sort: true}},
        {id: 'capacity', name: 'Вместимость', meta: {sort: true}},
        {
            id: 'status',
            name: 'Статус',
            meta: {sort: true},
            template: (item) => {
                return (<AsyncStatusLabel room={item}/>)
            }
        },
        {
            id: 'workload',
            name: 'Загруженность',
            align: 'center',
            meta: {sort: true},
            template: (item) => {
                const workload = item.workload ?? 0;
                const occupancy = getOccupancyLevel(workload);

                return (
                    <Tooltip content={`${occupancy.label} загрузка (${workload}%)`}>
                        <span>{occupancy.icon}</span>
                    </Tooltip>
                );
            }
        }, {id: 'description'}
    ];

    const getRowId = 'id';

    function changeSelectedIds(ids: string[]) {
        setSelectedIds(ids);
        if (onUpdate) {
            onUpdate(ids);
        }
    }

    return (
        <MyTable
            data={data}
            columns={columns}
            getRowId={getRowId}
            selectedIds={selectedIds}
            onSelectionChange={changeSelectedIds}
            emptyMessage={noData}
            edgePadding={true}
            onRowClick={(room, index, event) => {
                router.visit(`/offices/${room.office_id}/rooms/${room.id}`);
            }}/>
    );
}

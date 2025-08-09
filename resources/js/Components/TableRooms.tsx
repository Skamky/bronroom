import {Label, Table, TableColumnConfig, Tooltip, withTableSelection} from '@gravity-ui/uikit';
import React, {ReactElement, useEffect, useState} from 'react';
import {router} from "@inertiajs/react";
import {ensureRoomStatus} from "@/types/room";

interface TableRoomsProps {
    officeId: number | bigint,
    onUpdate?: (roomsId: (string[] | number[])) => void
}

interface RoomType {
    id: number;
    name?: string;
    capacity?: number;
    description?: string;
    office_id?: number;
    created_at?: string;
    updated_at?: string;
    status: string;
    workload: number
}


export default function TableRooms({officeId, onUpdate}: TableRoomsProps): ReactElement {
    const MyTable = withTableSelection<RoomType>(Table);

    const [data, setData] = useState([]);
    const [noData, setNoData] = useState('No data');
    const [selectedIds, setSelectedIds] = React.useState<string[]>([]);

    const getOccupancyLevel = (rate: number) => {
        if (rate <= 30) return {icon: '🟢', label: 'Низкая'};
        if (rate <= 70) return {icon: '🟡', label: 'Средняя'};
        if (rate <= 90) return {icon: '🟠', label: 'Высокая'};
        return {icon: '🔴', label: 'Очень высокая'};
    };

    let columns: TableColumnConfig<RoomType>[] = [
        {id: 'id'},
        {id: 'name', name: 'Название'},
        {id: 'capacity', name: 'Вместимость'},
        {
            id: 'status', name: 'Статус', template: (item) => {
                const status = ensureRoomStatus(item.status);
                return (<Label theme={status.theme}>{status.description}</Label>)
            }
        },
        {
            id: 'workload',
            name: 'Загруженность',
            align: 'center',
            template: (item) => {
                const workload = item.workload; // исходное значение workload
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

    useEffect(() => {
        if (officeId == 0) {
            return
        }
        setNoData('Loading...');
        setData([]);

        fetch(`/offices/${officeId}/rooms`, {
            headers: {
                'Accept': 'application/json'
            }
        }).then(response => {
            if (!(response.ok || response.status == 404)) {
                console.warn(response);
                throw new Error(`Network response was not ok \n status:${response.status} \n statusText: ${response.statusText}`);
            }
            return response.json();
        })
            .then(result => {
                if ('message' in result) {
                    setData([]);
                    setNoData('No data');
                    return;
                }
                setData(result);
            })
            .catch(err => {
                setData([]);
                setNoData(err.message);
            });
    }, [officeId]);

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

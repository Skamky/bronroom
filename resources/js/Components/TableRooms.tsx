import {Label, Table, TableDataItem, withTableSelection} from '@gravity-ui/uikit';
import React, {ReactElement, useEffect, useState} from 'react';
import {router} from "@inertiajs/react";
import {ensureRoomStatus} from "@/types/room";

interface TableRoomsProps {
    officeId: number | bigint,
    onUpdate?: (roomsId: (string[] | number[])) => void
}

interface RoomType extends TableDataItem {
    id: number;
    name?: string;
    capacity?: number;
    description?: string;
    office_id?: number;
    created_at?: string;
    updated_at?: string;
}


export default function TableRooms({officeId, onUpdate}: TableRoomsProps): ReactElement {

    const MyTable = withTableSelection(Table);

    const [data, setData] = useState([]);
    const [noData, setNoData] = useState('No data');
    const [selectedIds, setSelectedIds] = React.useState<string[]>([]);
    let columns = [
        {id: 'id'},
        {id: 'name', name: 'Название'},
        {id: 'capacity', name: 'Вместимость'},
        {id: 'status', name: 'Статус',},
        {id: 'workload'},
        {id: 'description'}
    ]

    const getRowId = 'id';


    useEffect(() => {
        if (officeId == 0) {
            return
        }
        setNoData('Loading...');
        setData([]);

        fetch(`/offices/${officeId}/rooms`, {
            headers: {
                'Accept': 'application/json' // Указывает, что вы ожидаете JSON в ответе
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
                const transformedData = result.map(room => {
                    const status = ensureRoomStatus(room.status);
                    return {
                        ...room,
                        status: <Label theme={status.theme}>{status.description}</Label>
                    };
                });

                setData(transformedData);
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




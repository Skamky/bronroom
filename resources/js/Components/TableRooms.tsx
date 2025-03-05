import {Table, TableDataItem, withTableSelection} from '@gravity-ui/uikit';
import React, {ReactElement, useEffect, useState} from 'react';
import {router} from "@inertiajs/react";

interface TableRoomsProps {
    officeId: number | bigint
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


export default function TableRooms({officeId}: TableRoomsProps): ReactElement {

    const MyTable = withTableSelection(Table);

    const [data, setData] = useState([]);
    const [noData, setNoData] = useState('No data');
    const [selectedIds, setSelectedIds] = React.useState<string[]>([]);
    let columns = [
        {id: 'id'},
        {id: 'name', name: 'Название'},
        {id: 'capacity', name: 'Вместимость'},
        {id: 'status'},
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
                setData(result);

                //Условия для 404 ошибки
                if ('message' in result) {
                    setData([])
                    setNoData('No data')
                    return;
                }


            })
            .catch(err => {
                setData([]);
                setNoData(err.message);
            });
    }, [officeId]);


    return (
        <MyTable
            data={data}
            columns={columns}
            getRowId={getRowId}
            selectedIds={selectedIds}
            onSelectionChange={setSelectedIds}
            emptyMessage={noData}
            edgePadding={true}
            onRowClick={(room, index, event) => {
                router.visit(`/offices/${room.office_id}/rooms/${room.id}`);
            }}/>
    );
}




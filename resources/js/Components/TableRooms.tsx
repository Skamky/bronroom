import {Table, withTableSelection} from '@gravity-ui/uikit';
import React, {ReactElement, useEffect, useState} from 'react';

interface TableRoomsProps {
    officeId: number | bigint
}


export default function TableRooms({officeId}: TableRoomsProps): ReactElement {

    const MyTable = withTableSelection(Table);

    const [data, setData] = useState([]);
    const [noData, setNoData] = useState('No data');
    const [selectedIds, setSelectedIds] = React.useState(['1']);

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

                if ('message' in result) {
                    setData([])
                    setNoData('No data')
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
            onRowClick={(item, index, event) => {
                console.log('Clicked item:', item);
                console.log('Row index:', index);
                console.log('Mouse event:', event);
            }}/>
    );
}




import {Table, withTableSelection} from '@gravity-ui/uikit';
import React from 'react';

export default function TableRooms() {

    const MyTable = withTableSelection(Table);

    let data = [
        {id: 1, text: 'Hello'},
        {id: 2, text: 'World'}
    ];

    let columns = [{id: 'id'}, {id: 'text'}]

    const getRowId = 'id';
    const [selectedIds, setSelectedIds] = React.useState(['1','2']);




        return (
            <MyTable
                data={data}
                columns={columns}
                getRowId={getRowId}
                selectedIds={selectedIds}
                onSelectionChange={setSelectedIds}
            />
        );
    }




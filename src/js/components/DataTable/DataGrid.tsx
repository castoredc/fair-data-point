import { DataGrid as MuiDataGrid, DataGridProps as MuiDataGridProps } from '@mui/x-data-grid';
import React from 'react';

interface DataGridProps extends MuiDataGridProps {
    emptyStateContent?: string;
    accessibleName?: string;
}


const DataGrid: React.FC<DataGridProps> = ({ emptyStateContent, rows, columns, ...rest }) => {
    return <MuiDataGrid
        // emptyStateContent={`This ${getType(type)} does not have any versions`}
        rows={rows}
        columns={columns}
        {...rest}
    />;
};

export default DataGrid;
import { DataGrid as MuiDataGrid, DataGridProps as MuiDataGridProps, GridOverlay } from '@mui/x-data-grid';
import { Alert, Box, CircularProgress, Typography } from '@mui/material';
import React from 'react';

interface DataGridProps extends Omit<MuiDataGridProps, 'slots'> {
    emptyStateContent?: React.ReactNode | (() => React.ReactNode);
    accessibleName?: string;
    loading?: boolean;
    error?: string | null;
}

const CustomNoRowsOverlay = ({ content }: { content: React.ReactNode | (() => React.ReactNode) }) => (
    <GridOverlay>
        <Box
            sx={{
                display: 'flex',
                flexDirection: 'column',
                alignItems: 'center',
                justifyContent: 'center',
                height: '100%',
                p: 2,
            }}
        >
            {typeof content === 'function' ? content() : content}
        </Box>
    </GridOverlay>
);

const CustomLoadingOverlay = () => (
    <GridOverlay>
        <Box
            sx={{
                display: 'flex',
                flexDirection: 'column',
                alignItems: 'center',
                justifyContent: 'center',
                height: '100%',
                p: 2,
            }}
        >
            <CircularProgress size={32} />
        </Box>
    </GridOverlay>
);

const CustomErrorOverlay = ({ error }: { error: string }) => (
    <GridOverlay>
        <Box
            sx={{
                display: 'flex',
                flexDirection: 'column',
                alignItems: 'center',
                justifyContent: 'center',
                height: '100%',
                p: 2,
            }}
        >
            <Alert severity="error" sx={{ maxWidth: 400 }}>
                {error}
            </Alert>
        </Box>
    </GridOverlay>
);

const DataGrid: React.FC<DataGridProps> = ({
                                               emptyStateContent = 'No data available',
                                               rows,
                                               columns,
                                               loading,
                                               error,
                                               ...rest
                                           }) => {
    const slots = {
        noRowsOverlay: () => <CustomNoRowsOverlay content={emptyStateContent} />,
        loadingOverlay: loading ? CustomLoadingOverlay : undefined,
    };

    return (
        <Box sx={{ width: '100%', height: '100%' }}>
            <MuiDataGrid
                rows={rows}
                columns={columns}
                loading={loading}
                slots={slots}
                disableRowSelectionOnClick
                {...rest}
            />
        </Box>
    );
};

export default DataGrid;
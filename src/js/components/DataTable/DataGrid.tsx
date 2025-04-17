import { DataGrid as MuiDataGrid, DataGridProps as MuiDataGridProps, GridOverlay } from '@mui/x-data-grid';
import { Alert, Box, CircularProgress, Typography } from '@mui/material';
import React from 'react';

interface DataGridProps extends MuiDataGridProps {
    emptyStateContent?: string;
    accessibleName?: string;
    loading?: boolean;
    error?: string | null;
}


const CustomNoRowsOverlay = ({ content }: { content: string }) => (
    <GridOverlay>
        <Box
            sx={{
                display: 'flex',
                flexDirection: 'column',
                alignItems: 'center',
                justifyContent: 'center',
                height: '100%',
                p: 2
            }}
        >
            <Typography color="text.secondary" align="center">
                {content}
            </Typography>
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
                p: 2
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
                p: 2
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
    const components = {
        NoRowsOverlay: () => <CustomNoRowsOverlay content={emptyStateContent} />,
        ...(loading && { LoadingOverlay: CustomLoadingOverlay }),
        ...(error && { ErrorOverlay: () => <CustomErrorOverlay error={error} /> })
    };

    return (
        <Box sx={{ width: '100%', height: '100%' }}>
            <MuiDataGrid
                rows={rows}
                columns={columns}
                loading={loading}
                error={error}
                components={components}
                disableSelectionOnClick
                {...rest}
            />
        </Box>
    );
};

export default DataGrid;
import React, { useEffect, useState } from 'react';
import Button from '@mui/material/Button';
import * as H from 'history';
import { localizedText } from '../../../../util';
import { isGranted } from 'utils/PermissionHelper';
import PageBody from 'components/Layout/Dashboard/PageBody';
import { apiClient } from 'src/js/network';
import AddIcon from '@mui/icons-material/Add';
import Stack from '@mui/material/Stack';
import withNotifications, { ComponentWithNotifications } from 'components/WithNotifications';
import DataGrid from 'components/DataTable/DataGrid';
import { GridColDef } from '@mui/x-data-grid';
import { Box } from '@mui/material';

interface Dataset {
    id: string;
    slug: string;
    hasMetadata: boolean;
    metadata?: {
        title: {
            [key: string]: string;
        };
    };
    permissions: string[];
}

interface DatasetsProps extends ComponentWithNotifications {
    catalog: string;
    history: H.History;
}

const Datasets: React.FC<DatasetsProps> = ({ catalog, history, notifications }) => {
    const [datasets, setDatasets] = useState<Dataset[]>([]);
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState<string | null>(null);

    const getDatasets = async () => {
        setLoading(true);
        setError(null);

        try {
            const response = await apiClient.get(`/api/catalog/${catalog}/dataset`);
            setDatasets(response.data.results);
        } catch (error: any) {
            const message = error.response?.data?.error || 'An error occurred while loading the datasets';
            setError(message);
            notifications.show(message, { variant: 'error' });
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        getDatasets();
    }, [catalog]);

    const columns: GridColDef<Dataset>[] = [
        {
            field: 'displayTitle',
            headerName: 'Title',
            flex: 1,
        },
    ];

    const rows = datasets.map(dataset => ({
        ...dataset,
        displayTitle: dataset.hasMetadata && dataset.metadata?.title
            ? localizedText(dataset.metadata.title, 'en') || 'Untitled dataset'
            : 'Untitled dataset',
    }));

    return (
        <PageBody>
            <Stack direction="row" sx={{ justifyContent: 'flex-end', mb: 2 }}>
                <Button
                    startIcon={<AddIcon />}
                    disabled={loading}
                    onClick={() => history.push(`/dashboard/catalogs/${catalog}/datasets/add`)}
                    variant="contained"
                >
                    Add dataset
                </Button>
            </Stack>

            <Box sx={{ height: 400, width: '100%' }}>
                <DataGrid
                    rows={rows}
                    columns={columns}
                    loading={loading}
                    error={error}
                    disableRowSelectionOnClick
                    emptyStateContent="This study does not have datasets"
                    onRowClick={(params) => {
                        if (isGranted('edit', params.row.permissions)) {
                            history.push(`/dashboard/catalogs/${catalog}/datasets/${params.row.slug}`);
                        }
                    }}
                    sx={{
                        cursor: 'pointer',
                        '& .MuiDataGrid-row': {
                            opacity: (theme) => {
                                const rowId = theme.palette.mode === 'light' ? theme.palette.grey[100] : theme.palette.grey[900];
                                const row = rows.find(r => r.id === rowId);
                                return row ? (isGranted('edit', row.permissions) ? 1 : 0.5) : 1;
                            },
                        },
                    }}
                />
            </Box>
        </PageBody>
    );
};

export default withNotifications(Datasets);
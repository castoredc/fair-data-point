import React, { useEffect, useState } from 'react';
import Button from '@mui/material/Button';
import AddIcon from '@mui/icons-material/Add';
import { localizedText } from '../../../util';
import { AuthorizedRouteComponentProps } from 'components/Route';
import { isGranted } from 'utils/PermissionHelper';
import PageBody from 'components/Layout/Dashboard/PageBody';
import { apiClient } from 'src/js/network';
import Stack from '@mui/material/Stack';
import withNotifications, { ComponentWithNotifications } from 'components/WithNotifications';
import DataGrid from 'components/DataTable/DataGrid';
import { GridColDef } from '@mui/x-data-grid';
import { Box } from '@mui/material';

interface Distribution {
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

interface DistributionsProps extends AuthorizedRouteComponentProps, ComponentWithNotifications {
}

const Distributions: React.FC<DistributionsProps> = ({ match, history, notifications }) => {
    const [distributions, setDistributions] = useState<Distribution[]>([]);
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState<string | null>(null);

    const mainUrl = match.params.study
        ? `/dashboard/studies/${match.params.study}/datasets/${match.params.dataset}`
        : `/dashboard/catalogs/${match.params.catalog}/datasets/${match.params.dataset}`;

    const getDistributions = async () => {
        setLoading(true);
        setError(null);

        try {
            const response = await apiClient.get(`/api/dataset/${match.params.dataset}/distribution`);
            setDistributions(response.data.results);
        } catch (error: any) {
            const message = error.response?.data?.error || 'An error occurred while loading the distributions';
            setError(message);
            notifications.show(message, { variant: 'error' });
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        getDistributions();
    }, [match.params.dataset]);

    const columns: GridColDef<Distribution>[] = [
        {
            field: 'displayTitle',
            headerName: 'Title',
            flex: 1,
        },
    ];

    const rows = distributions.map(distribution => ({
        ...distribution,
        displayTitle: distribution.hasMetadata && distribution.metadata?.title
            ? localizedText(distribution.metadata.title, 'en') || 'Untitled distribution'
            : 'Untitled distribution',
    }));

    return (
        <PageBody>
            <Stack direction="row" sx={{ justifyContent: 'flex-end', mb: 2 }}>
                <Button
                    startIcon={<AddIcon />}
                    disabled={loading}
                    onClick={() => history.push(`${mainUrl}/distributions/add`)}
                    variant="contained"
                >
                    New distribution
                </Button>
            </Stack>

            <Box sx={{ height: 400, width: '100%' }}>
                <DataGrid
                    rows={rows}
                    columns={columns}
                    loading={loading}
                    error={error}
                    disableRowSelectionOnClick
                    emptyStateContent="This study does not have distributions"
                    onRowClick={(params) => {
                        if (isGranted('edit', params.row.permissions)) {
                            history.push(`${mainUrl}/distributions/${params.row.slug}`);
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

export default withNotifications(Distributions);
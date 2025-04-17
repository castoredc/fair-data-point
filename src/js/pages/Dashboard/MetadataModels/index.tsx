import React, { useEffect, useState } from 'react';
import Button from '@mui/material/Button';
import DocumentTitle from 'components/DocumentTitle';
import { AuthorizedRouteComponentProps } from 'components/Route';
import { isAdmin } from 'utils/PermissionHelper';
import { apiClient } from 'src/js/network';
import DashboardPage from 'components/Layout/Dashboard/DashboardPage';
import DashboardSideBar from 'components/SideBar/DashboardSideBar';
import Body from 'components/Layout/Dashboard/Body';
import withNotifications, { ComponentWithNotifications } from 'components/WithNotifications';
import Header from 'components/Layout/Dashboard/Header';
import PageBody from 'components/Layout/Dashboard/PageBody';
import AddIcon from '@mui/icons-material/Add';
import DataGrid from 'components/DataTable/DataGrid';
import { GridColDef } from '@mui/x-data-grid';
import { Box } from '@mui/material';

interface MetadataModel {
    id: string;
    title: string;
    permissions: string[];
}

interface MetadataModelsProps extends AuthorizedRouteComponentProps, ComponentWithNotifications {}

const MetadataModels: React.FC<MetadataModelsProps> = ({ history, location, user, notifications }) => {
    const [metadataModels, setMetadataModels] = useState<MetadataModel[]>([]);
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState<string | null>(null);

    const columns: GridColDef<MetadataModel>[] = [
        { 
            field: 'title', 
            headerName: 'Title', 
            flex: 1,
        },
    ];

    const getMetadataModels = async () => {
        setLoading(true);
        setError(null);

        try {
            const response = await apiClient.get('/api/metadata-model/my');
            const mappedModels: MetadataModel[] = response.data.results
                .filter((model: any) => model.permissions.includes('edit'))
                .map((model: any) => ({
                    id: model.id,
                    title: model.title || 'Untitled model',
                    permissions: model.permissions
                }));
            setMetadataModels(mappedModels);
        } catch (error: any) {
            const errorMessage = error.response?.data?.error || 'An error occurred while loading your metadata models';
            setError(errorMessage);
            notifications.show(errorMessage, { variant: 'error' });
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        getMetadataModels();
    }, []);

    return (
        <DashboardPage>
            <DocumentTitle title="Metadata models" />
            <DashboardSideBar location={location} history={history} user={user} />

            <Body>
                <Header title="My metadata models">
                    {isAdmin(user) && (
                        <Button
                            startIcon={<AddIcon />}
                            onClick={() => history.push('/dashboard/metadata-models/add')}
                            variant="contained"
                        >
                            Add metadata model
                        </Button>
                    )}
                </Header>

                <PageBody>
                    <Box sx={{ height: 400, width: '100%' }}>
                        <DataGrid
                            rows={metadataModels}
                            columns={columns}
                            loading={loading}
                            error={error}
                            disableRowSelectionOnClick
                            emptyStateContent="No metadata models found"
                            onRowClick={(params) => history.push(`/dashboard/metadata-models/${params.row.id}`)}
                            sx={{ cursor: 'pointer' }}
                        />
                    </Box>
                </PageBody>
            </Body>
        </DashboardPage>
    );
};

export default withNotifications(MetadataModels);
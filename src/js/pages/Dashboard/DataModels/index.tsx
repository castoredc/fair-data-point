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

interface DataModel {
    id: string;
    title: string;
}

interface DataModelsProps extends AuthorizedRouteComponentProps, ComponentWithNotifications {}

const DataModels: React.FC<DataModelsProps> = ({ history, location, user, notifications }) => {
    const [dataModels, setDataModels] = useState<DataModel[]>([]);
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState<string | null>(null);

    const columns: GridColDef<DataModel>[] = [
        { 
            field: 'title', 
            headerName: 'Title', 
            flex: 1,
        },
    ];

    const getDataModels = async () => {
        setLoading(true);
        setError(null);

        try {
            const response = await apiClient.get('/api/data-model/my');
            const mappedModels: DataModel[] = response.data.map((model: any) => ({
                id: model.id,
                title: model.title || 'Untitled model'
            }));
            setDataModels(mappedModels);
        } catch (error: any) {
            const errorMessage = error.response?.data?.error || 'An error occurred while loading your data models';
            setError(errorMessage);
            notifications.show(errorMessage, { variant: 'error' });
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        getDataModels();
    }, []);

    return (
        <DashboardPage>
            <DocumentTitle title="Data models" />
            <DashboardSideBar location={location} history={history} user={user} />

            <Body>
                <Header title="My data models">
                    {isAdmin(user) && (
                        <Button
                            startIcon={<AddIcon />}
                            onClick={() => history.push('/dashboard/data-models/add')}
                            variant="contained"
                        >
                            Add data model
                        </Button>
                    )}
                </Header>

                <PageBody>
                    <Box sx={{ height: 400, width: '100%' }}>
                        <DataGrid
                            rows={dataModels}
                            columns={columns}
                            loading={loading}
                            error={error}
                            disableRowSelectionOnClick
                            emptyStateContent="No data models found"
                            onRowClick={(params) => history.push(`/dashboard/data-models/${params.row.id}`)}
                            sx={{ cursor: 'pointer' }}
                        />
                    </Box>
                </PageBody>
            </Body>
        </DashboardPage>
    );
};

export default withNotifications(DataModels);
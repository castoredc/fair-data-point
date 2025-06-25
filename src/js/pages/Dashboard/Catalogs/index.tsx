import React, { useEffect, useState } from 'react';
import Button from '@mui/material/Button';
import DocumentTitle from 'components/DocumentTitle';
import { localizedText } from '../../../util';
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

interface Catalog {
    id: string;
    slug: string;
    hasMetadata: boolean;
    metadata?: {
        title: {
            [key: string]: string;
        };
    };
}

interface CatalogsProps extends AuthorizedRouteComponentProps, ComponentWithNotifications {
}

const Catalogs: React.FC<CatalogsProps> = ({ history, location, user, notifications }) => {
    const [catalogs, setCatalogs] = useState<Catalog[]>([]);
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState<string | null>(null);
    const [paginationModel, setPaginationModel] = useState({
        page: 0,
        pageSize: 25,
    });

    const columns: GridColDef<Catalog>[] = [
        {
            field: 'displayTitle',
            headerName: 'Title',
            flex: 1,
        },
    ];

    const getCatalogs = async () => {
        setLoading(true);
        setError(null);

        try {
            const response = await apiClient.get('/api/catalog/my', {
                params: {
                    page: paginationModel.page + 1,
                    perPage: paginationModel.pageSize,
                },
            });

            const mappedCatalogs: Catalog[] = response.data.results.map((catalog: any) => ({
                id: catalog.id,
                ...catalog,
                displayTitle: catalog.hasMetadata
                    ? localizedText(catalog.metadata?.title, 'en')
                    : '(no title)',
            }));
            setCatalogs(mappedCatalogs);
        } catch (error: any) {
            const errorMessage = error.response?.data?.error || 'An error occurred while loading your catalogs';
            setError(errorMessage);
            notifications.show(errorMessage, { variant: 'error' });
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        getCatalogs();
    }, [paginationModel.page, paginationModel.pageSize]);


    return (
        <DashboardPage>
            <DocumentTitle title="Catalogs" />
            <DashboardSideBar location={location} history={history} user={user} />

            <Body>
                <Header title="My catalogs">
                    {isAdmin(user) && (
                        <Button
                            startIcon={<AddIcon />}
                            onClick={() => history.push('/dashboard/catalogs/add')}
                            variant="contained"
                        >
                            Add catalog
                        </Button>
                    )}
                </Header>

                <PageBody>
                    <Box sx={{ height: 400, width: '100%' }}>
                        <DataGrid
                            rows={catalogs}
                            columns={columns}
                            loading={loading}
                            error={error}
                            paginationModel={paginationModel}
                            onPaginationModelChange={setPaginationModel}
                            pageSizeOptions={[10, 25, 50]}
                            disableRowSelectionOnClick
                            emptyStateContent="No catalogs found"
                            onRowClick={(params) => history.push(`/dashboard/catalogs/${params.row.slug}`)}
                            sx={{ cursor: 'pointer' }}
                        />
                    </Box>
                </PageBody>
            </Body>
        </DashboardPage>
    );
};

export default withNotifications(Catalogs);
import React, { useEffect, useState } from 'react';
import Button from '@mui/material/Button';
import DocumentTitle from 'components/DocumentTitle';
import { isAdmin } from 'utils/PermissionHelper';
import { AuthorizedRouteComponentProps } from 'components/Route';
import { apiClient } from 'src/js/network';
import { localizedText } from '../../../util';
import Body from 'components/Layout/Dashboard/Body';
import DashboardSideBar from 'components/SideBar/DashboardSideBar';
import DashboardPage from 'components/Layout/Dashboard/DashboardPage';
import withNotifications, { ComponentWithNotifications } from 'components/WithNotifications';
import { Box, Checkbox, FormControlLabel } from '@mui/material';
import Header from 'components/Layout/Dashboard/Header';
import PageBody from 'components/Layout/Dashboard/PageBody';
import AddIcon from '@mui/icons-material/Add';
import DataGrid from 'components/DataTable/DataGrid';
import { GridColDef } from '@mui/x-data-grid';


interface Study {
    id: string;
    name: string;
    hasMetadata: boolean;
    metadata?: {
        title: {
            [key: string]: string;
        };
    };
}

interface StudiesProps extends AuthorizedRouteComponentProps, ComponentWithNotifications {
}

const Studies: React.FC<StudiesProps> = ({ history, location, user, notifications }) => {
    const [studies, setStudies] = useState<Study[]>([]);
    const [loading, setLoading] = useState(false);
    const [viewAll, setViewAll] = useState(isAdmin(user));
    const [error, setError] = useState<string | null>(null);
    const [paginationModel, setPaginationModel] = useState({
        page: 0,
        pageSize: 25,
    });

    const columns: GridColDef<Study>[] = [
        {
            field: 'displayTitle',
            headerName: 'Title',
            flex: 1,
        },
    ];

    const getStudies = async () => {
        setLoading(true);
        setError(null);

        try {
            const response = await apiClient.get(viewAll ? '/api/study' : '/api/study/my', {
                params: {
                    page: paginationModel.page + 1,
                    perPage: paginationModel.pageSize,
                },
            });

            const mappedStudies: Study[] = response.data.results.map((study: any) => ({
                id: study.id,
                ...study,
                displayTitle: study.hasMetadata
                    ? localizedText(study.metadata?.title, 'en') || 'Untitled study'
                    : study.name || 'Untitled study',
            }));
            setStudies(mappedStudies);
        } catch (error: any) {
            const errorMessage = error.response?.data?.error || 'An error occurred while loading your studies';
            setError(errorMessage);
            notifications.show(errorMessage, { variant: 'error' });
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        getStudies();
    }, [viewAll, paginationModel.page, paginationModel.pageSize]);

    const handleView = () => {
        setViewAll(!viewAll);
    };

    return (
        <DashboardPage>
            <DocumentTitle title="Studies" />
            <DashboardSideBar location={location} history={history} user={user} />

            <Body>
                <Header
                    title="My studies"
                    badge={
                        isAdmin(user) ? (
                            <FormControlLabel
                                control={
                                    <Checkbox
                                        name="viewAll"
                                        onChange={handleView}
                                        checked={viewAll}
                                    />
                                }
                                label="View all studies"
                            />
                        ) : undefined
                    }
                >
                    <Button
                        startIcon={<AddIcon />}
                        onClick={() => history.push('/dashboard/studies/add')}
                        variant="contained"
                    >
                        Add study
                    </Button>
                </Header>

                <PageBody>
                    <Box sx={{ height: 400, width: '100%' }}>
                        <DataGrid
                            rows={studies}
                            columns={columns}
                            loading={loading}
                            error={error}
                            paginationModel={paginationModel}
                            onPaginationModelChange={setPaginationModel}
                            pageSizeOptions={[10, 25, 50]}
                            disableRowSelectionOnClick
                            emptyStateContent={viewAll ? 'No studies found' : 'You have no studies'}
                            onRowClick={(params) => history.push(`/dashboard/studies/${params.row.id}`)}
                            sx={{ cursor: 'pointer' }}
                        />
                    </Box>
                </PageBody>
            </Body>
        </DashboardPage>
    );
};

export default withNotifications(Studies);
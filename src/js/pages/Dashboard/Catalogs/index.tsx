import React, { Component } from 'react';
import Button from '@mui/material/Button';
import Pagination from '@mui/material/Pagination';
import LoadingOverlay from 'components/LoadingOverlay';
import ListItem from 'components/ListItem';
import DocumentTitle from 'components/DocumentTitle';
import DataGridHelper from 'components/DataTable/DataGridHelper';
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

interface CatalogsProps extends AuthorizedRouteComponentProps, ComponentWithNotifications {
}

interface CatalogsState {
    catalogs: any;
    isLoading: boolean;
    pagination: any;
}

class Catalogs extends Component<CatalogsProps, CatalogsState> {
    constructor(props) {
        super(props);

        this.state = {
            catalogs: [],
            isLoading: false,
            pagination: DataGridHelper.getDefaultState(25),
        };
    }

    getCatalogs = () => {
        const { notifications } = this.props;
        const { pagination } = this.state;

        this.setState({
            isLoading: true,
        });

        let filters = {
            page: pagination.currentPage,
            perPage: pagination.perPage,
        };

        apiClient
            .get('/api/catalog/my', { params: filters })
            .then(response => {
                this.setState({
                    catalogs: response.data.results,
                    pagination: DataGridHelper.parseResults(response.data),
                    isLoading: false,
                });
            })
            .catch(error => {
                this.setState({
                    isLoading: false,
                });

                if (error.response && typeof error.response.data.error !== 'undefined') {
                    notifications.show(error.response.data.error, { variant: 'error' });
                } else {
                    notifications.show('An error occurred while loading your catalogs', { variant: 'error' });
                }
            });
    };

    handlePagination = (event: React.ChangeEvent<unknown>, value: number) => {
        const { pagination } = this.state;

        this.setState(
            {
                pagination: {
                    ...pagination,
                    currentPage: value,
                    perPage: pagination.pageSize,
                },
            },
            () => {
                this.getCatalogs();
            },
        );
    };

    componentDidMount() {
        this.getCatalogs();
    }

    render() {
        const { location, history, user } = this.props;
        const { isLoading, catalogs, pagination } = this.state;

        return (
            <DashboardPage>
                <DocumentTitle title="Catalogs" />

                {isLoading && <LoadingOverlay accessibleLabel="Loading catalogs" />}

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
                        {catalogs.map(catalog => {
                            return (
                                <ListItem
                                    selectable={false}
                                    link={`/dashboard/catalogs/${catalog.slug}`}
                                    title={catalog.hasMetadata ? localizedText(catalog.metadata.title, 'en') : '(no title)'}
                                    key={catalog.id}
                                />
                            );
                        })}

                        {catalogs.length == 0 && <div className="NoResults">No catalogs found.</div>}

                        {pagination && (
                            <Pagination
                                onChange={this.handlePagination}
                                page={pagination.currentPage - 1}
                                count={pagination.totalResults}
                            />
                        )}
                    </PageBody>
                </Body>
            </DashboardPage>
        );
    }
}

export default withNotifications(Catalogs);
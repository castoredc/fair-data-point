import React, { Component } from 'react';
import Button from '@mui/material/Button';
import LoadingOverlay from 'components/LoadingOverlay';
import ListItem from 'components/ListItem';
import DocumentTitle from 'components/DocumentTitle';
import DataGridHelper from 'components/DataTable/DataGridHelper';
import { isAdmin } from 'utils/PermissionHelper';
import ScrollShadow from 'components/ScrollShadow';
import { AuthorizedRouteComponentProps } from 'components/Route';
import { apiClient } from 'src/js/network';
import { localizedText } from '../../../util';
import Body from 'components/Layout/Dashboard/Body';
import DashboardSideBar from 'components/SideBar/DashboardSideBar';
import DashboardPage from 'components/Layout/Dashboard/DashboardPage';
import withNotifications, { ComponentWithNotifications } from 'components/WithNotifications';
import Pagination from '@mui/material/Pagination';
import { Checkbox, FormControlLabel } from '@mui/material';
import Header from 'components/Layout/Dashboard/Header';
import PageBody from 'components/Layout/Dashboard/PageBody';
import AddIcon from '@mui/icons-material/Add';
import List from '@mui/material/List';

interface StudiesProps extends AuthorizedRouteComponentProps, ComponentWithNotifications {
}

interface StudiesState {
    studies: any;
    isLoading: boolean;
    pagination: any;
    viewAll: boolean;
}

class Studies extends Component<StudiesProps, StudiesState> {
    constructor(props) {
        super(props);

        this.state = {
            studies: [],
            isLoading: false,
            viewAll: isAdmin(props.user),
            pagination: DataGridHelper.getDefaultState(25),
        };
    }

    getStudies = () => {
        const { notifications } = this.props;
        const { viewAll, pagination } = this.state;

        this.setState({
            isLoading: true,
        });

        apiClient
            .get(viewAll ? '/api/study' : '/api/study/my', {
                params: {
                    page: pagination.currentPage,
                    perPage: pagination.perPage,
                },
            })
            .then(response => {
                this.setState({
                    studies: response.data.results,
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
                    notifications.show('An error occurred while loading your studies', { variant: 'error' });
                }
            });
    };

    handleView = () => {
        const { viewAll } = this.state;

        this.setState(
            {
                viewAll: !viewAll,
            },
            () => {
                this.getStudies();
            },
        );
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
                this.getStudies();
            },
        );
    };

    componentDidMount() {
        this.getStudies();
    }

    render() {
        const { history, location, user } = this.props;
        const { isLoading, studies, pagination, viewAll } = this.state;

        return (
            <DashboardPage>
                <DocumentTitle title="Studies" />

                {isLoading && <LoadingOverlay accessibleLabel="Loading studies" />}

                <DashboardSideBar location={location} history={history} user={user} />

                <Body>
                    <Header
                        title="My studies"

                        badge={isAdmin(user) ?
                            <FormControlLabel
                                control={<Checkbox name="viewAll"
                                                   onChange={this.handleView}
                                                   checked={viewAll} />}
                                label="View all studies"
                            /> : undefined}
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
                        <List sx={{ width: '100%' }}>
                            {studies.map(study => {
                                let title = study.hasMetadata ? localizedText(study.metadata.title, 'en') : study.name;

                                if (title === '') {
                                    title = 'Untitled study';
                                }

                                return (
                                    <ListItem
                                        key={study.id}
                                        selectable={false}
                                        link={`/dashboard/studies/${study.id}`}
                                        title={title}
                                    />
                                );
                            })}

                            {studies.length == 0 && <div className="NoResults">No studies found.</div>}
                        </List>

                        <div className="DashboardFooter">
                            {pagination && (
                                <Pagination
                                    onChange={this.handlePagination}
                                    page={pagination.currentPage - 1}
                                    count={pagination.totalResults}
                                />
                            )}
                        </div>
                    </PageBody>
                </Body>
            </DashboardPage>
        );
    }
}

export default withNotifications(Studies);
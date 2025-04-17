import React, { Component } from 'react';
import Button from '@mui/material/Button';
import LoadingOverlay from 'components/LoadingOverlay';
import ListItem from 'components/ListItem';
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

interface DataModelsProps extends AuthorizedRouteComponentProps, ComponentWithNotifications {
}

interface DataModelsState {
    dataModels: any;
    isLoading: boolean;
}

class DataModels extends Component<DataModelsProps, DataModelsState> {
    constructor(props) {
        super(props);

        this.state = {
            dataModels: [],
            isLoading: false,
        };
    }

    getDataModels = () => {
        const { notifications } = this.props;

        this.setState({
            isLoading: true,
        });

        apiClient
            .get('/api/data-model/my')
            .then(response => {
                this.setState({
                    dataModels: response.data,
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
                    notifications.show('An error occurred while loading your data models', { variant: 'error' });
                }
            });
    };

    componentDidMount() {
        this.getDataModels();
    }

    render() {
        const { location, history, user } = this.props;
        const { isLoading, dataModels } = this.state;

        return (
            <DashboardPage>
                <DocumentTitle title="Data models" />

                {isLoading && <LoadingOverlay accessibleLabel="Loading data models" />}


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
                        {dataModels.map(model => {
                            return <ListItem selectable={false} link={`/dashboard/data-models/${model.id}`}
                                             title={model.title} />;
                        })}

                        {dataModels.length == 0 && <div className="NoResults">No data models found.</div>}
                    </PageBody>
                </Body>
            </DashboardPage>
        );
    }
}

export default withNotifications(DataModels);
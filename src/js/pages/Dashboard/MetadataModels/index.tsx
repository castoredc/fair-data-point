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

interface MetadataModelsProps extends AuthorizedRouteComponentProps, ComponentWithNotifications {
}

interface MetadataModelsState {
    metadataModels: any;
    isLoading: boolean;
}

class MetadataModels extends Component<MetadataModelsProps, MetadataModelsState> {
    constructor(props) {
        super(props);

        this.state = {
            metadataModels: [],
            isLoading: false,
        };
    }

    getMetadataModels = () => {
        const { notifications } = this.props;

        this.setState({
            isLoading: true,
        });

        apiClient
            .get('/api/metadata-model/my')
            .then(response => {
                this.setState({
                    metadataModels: response.data.results.filter(model => model.permissions.includes('edit')),
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
                    notifications.show('An error occurred while loading your metadata models', { variant: 'error' });
                }
            });
    };

    componentDidMount() {
        this.getMetadataModels();
    }

    render() {
        const { location, history, user } = this.props;
        const { isLoading, metadataModels } = this.state;

        return (
            <DashboardPage>
                <DocumentTitle title="Metadata models" />

                {isLoading && <LoadingOverlay accessibleLabel="Loading metadata models" />}


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
                        {metadataModels.map(model => {
                            return <ListItem selectable={false} link={`/dashboard/metadata-models/${model.id}`}
                                             title={model.title} />;
                        })}

                        {metadataModels.length == 0 && <div className="NoResults">No metadata models found.</div>}
                    </PageBody>
                </Body>
            </DashboardPage>
        );
    }
}

export default withNotifications(MetadataModels);
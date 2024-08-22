import React, { Component } from 'react';
import { toast } from 'react-toastify';
import ToastItem from 'components/ToastItem';
import { Button, LoadingOverlay, Space } from '@castoredc/matter';
import ListItem from 'components/ListItem';
import DocumentTitle from 'components/DocumentTitle';
import { AuthorizedRouteComponentProps } from 'components/Route';
import { isAdmin } from 'utils/PermissionHelper';
import { apiClient } from 'src/js/network';
import DashboardTab from 'components/Layout/DashboardTab';
import DashboardTabHeader from 'components/Layout/DashboardTab/DashboardTabHeader';

interface MetadataModelsProps extends AuthorizedRouteComponentProps {}

interface MetadataModelsState {
    metadataModels: any;
    isLoading: boolean;
}

export default class MetadataModels extends Component<MetadataModelsProps, MetadataModelsState> {
    constructor(props) {
        super(props);

        this.state = {
            metadataModels: [],
            isLoading: false,
        };
    }

    getMetadataModels = () => {
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
                    toast.error(<ToastItem type="error" title={error.response.data.error} />);
                } else {
                    toast.error(<ToastItem type="error" title="An error occurred while loading your metadata models" />);
                }
            });
    };

    componentDidMount() {
        this.getMetadataModels();
    }

    render() {
        const { history, user } = this.props;
        const { isLoading, metadataModels } = this.state;

        return (
            <DashboardTab>
                <DocumentTitle title="Metadata models" />

                {isLoading && <LoadingOverlay accessibleLabel="Loading metadata models" />}

                <Space bottom="comfortable" />

                <DashboardTabHeader title="My metadata models" type="Section">
                    {isAdmin(user) && (
                        <Button buttonType="primary" onClick={() => history.push('/dashboard/metadata-models/add')}>
                            Add metadata model
                        </Button>
                    )}
                </DashboardTabHeader>

                <div>
                    {metadataModels.map(model => {
                        return <ListItem selectable={false} link={`/dashboard/metadata-models/${model.id}`} title={model.title} />;
                    })}

                    {metadataModels.length == 0 && <div className="NoResults">No metadata models found.</div>}
                </div>
            </DashboardTab>
        );
    }
}

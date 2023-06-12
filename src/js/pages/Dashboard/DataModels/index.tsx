import React, { Component } from 'react';
import { toast } from 'react-toastify';
import {Button, LoadingOverlay, Space, ToastMessage} from '@castoredc/matter';
import ListItem from 'components/ListItem';
import DocumentTitle from 'components/DocumentTitle';
import Header from 'components/Layout/Dashboard/Header';
import { AuthorizedRouteComponentProps } from 'components/Route';
import { isAdmin } from 'utils/PermissionHelper';
import PageBody from 'components/Layout/Dashboard/PageBody';
import { apiClient } from 'src/js/network';

interface DataModelsProps extends AuthorizedRouteComponentProps {}

interface DataModelsState {
    dataModels: any;
    isLoading: boolean;
}

export default class DataModels extends Component<DataModelsProps, DataModelsState> {
    constructor(props) {
        super(props);

        this.state = {
            dataModels: [],
            isLoading: false,
        };
    }

    getDataModels = () => {
        this.setState({
            isLoading: true,
        });

        apiClient
            .get('/api/model/my')
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
                    toast.error(<ToastMessage type="error" title={error.response.data.error} />);
                } else {
                    toast.error(<ToastMessage type="error" title="An error occurred while loading your data models" />);
                }
            });
    };

    componentDidMount() {
        this.getDataModels();
    }

    render() {
        const { history, user } = this.props;
        const { isLoading, dataModels } = this.state;

        return (
            <PageBody>
                <DocumentTitle title="Data models" />

                {isLoading && <LoadingOverlay accessibleLabel="Loading data models" />}

                <Space bottom="comfortable" />

                <Header title="My data models" type="Section">
                    {isAdmin(user) && (
                        <Button buttonType="primary" onClick={() => history.push('/dashboard/data-models/add')}>
                            Add data model
                        </Button>
                    )}
                </Header>

                <div>
                    {dataModels.map(model => {
                        return <ListItem selectable={false} link={`/dashboard/data-models/${model.id}`} title={model.title} />;
                    })}

                    {dataModels.length == 0 && <div className="NoResults">No data models found.</div>}
                </div>
            </PageBody>
        );
    }
}

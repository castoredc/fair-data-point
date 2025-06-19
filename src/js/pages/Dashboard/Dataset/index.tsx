import React, { Component } from 'react';
import Button from '@mui/material/Button';
import LoadingOverlay from 'components/LoadingOverlay';
import { Route, Switch } from 'react-router-dom';
import DocumentTitle from 'components/DocumentTitle';
import { localizedText } from '../../../util';
import Header from 'components/Layout/Dashboard/Header';
import Body from 'components/Layout/Dashboard/Body';
import DatasetForm from 'components/Form/Admin/DatasetForm';
import SideBar from 'components/SideBar';
import NotFound from 'pages/ErrorPages/NotFound';
import Distributions from 'pages/Dashboard/Dataset/Distributions';
import AddDistribution from 'pages/Dashboard/Dataset/AddDistribution';
import { AuthorizedRouteComponentProps } from 'components/Route';
import { isGranted } from 'utils/PermissionHelper';
import PermissionEditor from 'components/PermissionEditor';
import NoPermission from 'pages/ErrorPages/NoPermission';
import PageBody from 'components/Layout/Dashboard/PageBody';
import { apiClient } from 'src/js/network';
import { Edit, Manage, View } from 'components/PermissionEditor/Permissions';
import MetadataForm from 'components/Form/Metadata/MetadataForm';
import OpenInNewIcon from '@mui/icons-material/OpenInNew';
import DashboardPage from 'components/Layout/Dashboard/DashboardPage';
import withNotifications, { ComponentWithNotifications } from 'components/WithNotifications';
import DescriptionIcon from '@mui/icons-material/Description';
import GroupsIcon from '@mui/icons-material/Groups';
import DatasetIcon from '@mui/icons-material/Dataset';
import FileOpenIcon from '@mui/icons-material/FileOpen';

interface DatasetProps extends AuthorizedRouteComponentProps, ComponentWithNotifications {
    study?: any;
    catalog?: any;
}

interface DatasetState {
    dataset: any;
    isLoading: boolean;
}

class Dataset extends Component<DatasetProps, DatasetState> {
    constructor(props) {
        super(props);

        this.state = {
            dataset: null,
            isLoading: true,
        };
    }

    getDataset = () => {
        this.setState({
            isLoading: true,
        });

        const { match, notifications } = this.props;

        apiClient
            .get('/api/dataset/' + match.params.dataset)
            .then(response => {
                this.setState({
                    dataset: response.data,
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
                    notifications.show('An error occurred while loading your dataset', { variant: 'error' });
                }
            });
    };

    componentDidMount() {
        this.getDataset();
    }

    render() {
        const { history, location, match, user } = this.props;
        const { isLoading, dataset } = this.state;

        if (!match.params.study && !match.params.catalog) {
            return null;
        }

        if (isLoading) {
            return <LoadingOverlay accessibleLabel="Loading dataset" />;
        }

        if (!isGranted('edit', dataset.permissions)) {
            return <NoPermission text="You do not have permission to edit this dataset" />;
        }

        const title = dataset.hasMetadata ? localizedText(dataset.metadata.title, 'en') : 'Untitled dataset';

        const mainUrl = match.params.study ? '/dashboard/studies/' + match.params.study : '/dashboard/catalogs/' + match.params.catalog;

        return (
            <DashboardPage>
                <DocumentTitle title={title} />

                <SideBar
                    back={{
                        to: mainUrl,
                        title: match.params.study ? 'Back to study' : 'Back to catalog',
                    }}
                    location={location}
                    items={[
                        {
                            to: mainUrl + '/datasets/' + dataset.slug,
                            exact: true,
                            title: 'Dataset',
                            icon: <DatasetIcon />,
                        },
                        {
                            to: mainUrl + '/datasets/' + dataset.slug + '/metadata',
                            exact: true,
                            title: 'Metadata',
                            icon: <DescriptionIcon />,
                        },
                        ...(isGranted('manage', dataset.permissions)
                            ? [
                                {
                                    to: mainUrl + '/datasets/' + dataset.slug + '/permissions',
                                    exact: true,
                                    title: 'Permissions',
                                    icon: <GroupsIcon />,
                                },
                            ]
                            : []),
                        {
                            type: 'separator',
                        },
                        {
                            to: mainUrl + '/datasets/' + dataset.slug + '/distributions',
                            exact: true,
                            title: 'Distributions',
                            icon: <FileOpenIcon />,
                        },
                    ]}
                    history={history}
                    user={user}
                />

                <Body>
                    <Header title={title}>
                        <Button variant="text" startIcon={<OpenInNewIcon />} href={`/fdp/dataset/${dataset.slug}`}
                                target="_blank">
                            View
                        </Button>
                    </Header>

                    <Switch>
                        <Route
                            path={['/dashboard/studies/:study/datasets/:dataset', '/dashboard/catalogs/:catalog/datasets/:dataset']}
                            exact
                            render={props => (
                                <PageBody>
                                    <DatasetForm dataset={dataset} mainUrl={mainUrl} {...props} />
                                </PageBody>
                            )}
                        />
                        <Route
                            path={['/dashboard/studies/:study/datasets/:dataset/metadata', '/dashboard/catalogs/:catalog/datasets/:dataset/metadata']}
                            exact
                            render={props => (
                                <PageBody>
                                    <MetadataForm type="dataset" object={dataset} onCreate={this.getDataset}
                                                  onSave={this.getDataset} />
                                </PageBody>
                            )}
                        />
                        <Route
                            path={[
                                '/dashboard/studies/:study/datasets/:dataset/permissions',
                                '/dashboard/catalogs/:catalog/datasets/:dataset/permissions',
                            ]}
                            exact
                            render={props =>
                                isGranted('manage', dataset.permissions) ? (
                                    <PermissionEditor
                                        getObject={this.getDataset}
                                        type="dataset"
                                        object={dataset}
                                        user={user}
                                        permissions={[View, Edit, Manage]}
                                        {...props}
                                    />
                                ) : (
                                    <NoPermission text="You do not have access to this page" />
                                )
                            }
                        />
                        <Route
                            path={[
                                '/dashboard/studies/:study/datasets/:dataset/distributions',
                                '/dashboard/catalogs/:catalog/datasets/:dataset/distributions',
                            ]}
                            exact
                            render={props => <Distributions {...props} user={user} />}
                        />
                        <Route
                            path={[
                                '/dashboard/studies/:study/datasets/:dataset/distributions/add',
                                '/dashboard/catalogs/:catalog/datasets/:dataset/distributions/add',
                            ]}
                            exact
                            render={props => <AddDistribution {...props} user={user} />}
                        />

                        <Route component={NotFound} />
                    </Switch>
                </Body>
            </DashboardPage>
        );
    }
}

export default withNotifications(Dataset);
import React, { Component } from 'react';
import { localizedText } from '../../../../util';
import { Route, Switch } from 'react-router-dom';
import LoadingOverlay from 'components/LoadingOverlay';
import DocumentTitle from 'components/DocumentTitle';
import SideBar from 'components/SideBar';
import NotFound from 'pages/ErrorPages/NotFound';
import CatalogForm from 'components/Form/Admin/CatalogForm';
import AddStudy from 'pages/Dashboard/Catalogs/Catalog/AddStudy';
import Studies from 'pages/Dashboard/Catalogs/Catalog/Studies';
import Datasets from 'pages/Dashboard/Catalogs/Catalog/Datasets';
import AddDataset from 'pages/Dashboard/Catalogs/Catalog/AddDataset';
import Body from 'components/Layout/Dashboard/Body';
import Header from 'components/Layout/Dashboard/Header';
import { AuthorizedRouteComponentProps } from 'components/Route';
import { isGranted } from 'utils/PermissionHelper';
import PermissionEditor from 'components/PermissionEditor';
import NoPermission from 'pages/ErrorPages/NoPermission';
import PageBody from 'components/Layout/Dashboard/PageBody';
import { apiClient } from 'src/js/network';
import { Edit, Manage, View } from 'components/PermissionEditor/Permissions';
import MetadataForm from 'components/Form/Metadata/MetadataForm';
import DashboardPage from 'components/Layout/Dashboard/DashboardPage';
import withNotifications, { ComponentWithNotifications } from 'components/WithNotifications';
import InventoryIcon from '@mui/icons-material/Inventory';
import DescriptionIcon from '@mui/icons-material/Description';
import GroupsIcon from '@mui/icons-material/Groups';
import BiotechIcon from '@mui/icons-material/Biotech';
import DatasetIcon from '@mui/icons-material/Dataset';

interface CatalogProps extends AuthorizedRouteComponentProps, ComponentWithNotifications {
}

interface CatalogState {
    catalog: any;
    isLoading: boolean;
}

class Catalog extends Component<CatalogProps, CatalogState> {
    constructor(props) {
        super(props);
        this.state = {
            isLoading: true,
            catalog: null,
        };
    }

    componentDidMount() {
        this.getCatalog();
    }

    getCatalog = () => {
        const { notifications } = this.props;

        this.setState({
            isLoading: true,
        });

        apiClient
            .get('/api/catalog/' + this.props.match.params.catalog)
            .then(response => {
                this.setState({
                    catalog: response.data,
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
                    notifications.show('An error occurred while loading the catalog', { variant: 'error' });
                }
            });
    };

    render() {
        const { catalog, isLoading } = this.state;
        const { location, history, user } = this.props;

        if (isLoading) {
            return <LoadingOverlay accessibleLabel="Loading catalog" />;
        }

        if (!isGranted('edit', catalog.permissions)) {
            return <NoPermission text="You do not have permission to edit this catalog" />;
        }

        const title = catalog.hasMetadata ? localizedText(catalog.metadata.title, 'en') : 'Untitled catalog';

        return (
            <DashboardPage>
                <DocumentTitle title={title} />

                <SideBar
                    back={{
                        to: '/dashboard/catalogs',
                        title: 'Back to catalog list',
                    }}
                    location={location}
                    items={[
                        {
                            to: '/dashboard/catalogs/' + catalog.slug,
                            exact: true,
                            title: 'Catalog',
                            icon: <InventoryIcon />,
                        },
                        {
                            to: '/dashboard/catalogs/' + catalog.slug + '/metadata',
                            exact: true,
                            title: 'Metadata',
                            icon: <DescriptionIcon />,
                        },
                        ...(isGranted('manage', catalog.permissions)
                            ? [
                                {
                                    to: '/dashboard/catalogs/' + catalog.slug + '/permissions',
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
                            to: '/dashboard/catalogs/' + catalog.slug + '/datasets',
                            exact: true,
                            title: 'Datasets',
                            icon: <DatasetIcon />,
                        },
                        {
                            to: '/dashboard/catalogs/' + catalog.slug + '/studies',
                            exact: true,
                            title: 'Studies',
                            icon: <BiotechIcon />,
                        },
                    ]}
                    history={history}
                />

                <Body>
                    <Header title={title} />

                    <Switch>
                        <Route
                            path="/dashboard/catalogs/:catalog"
                            exact
                            render={props => (
                                <PageBody>
                                    <CatalogForm catalog={catalog} history={history} />
                                </PageBody>
                            )}
                        />
                        <Route
                            path="/dashboard/catalogs/:catalog/metadata"
                            exact
                            render={props => (
                                <PageBody>
                                    <MetadataForm type="catalog" object={catalog} onCreate={this.getCatalog}
                                                  onSave={this.getCatalog} />
                                </PageBody>
                            )}
                        />
                        <Route
                            path="/dashboard/catalogs/:catalog/permissions"
                            exact
                            render={props =>
                                isGranted('manage', catalog.permissions) ? (
                                    <PermissionEditor
                                        getObject={this.getCatalog}
                                        type="catalog"
                                        object={catalog}
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
                            path="/dashboard/catalogs/:catalog/studies/add"
                            exact
                            render={props => <AddStudy {...props} catalog={catalog.slug} user={user} />}
                        />
                        <Route path="/dashboard/catalogs/:catalog/studies" exact
                               render={props => <Studies {...props} catalog={catalog.slug} />} />
                        <Route path="/dashboard/catalogs/:catalog/datasets" exact
                               render={props => <Datasets {...props} catalog={catalog.slug} />} />
                        <Route
                            path="/dashboard/catalogs/:catalog/datasets/add"
                            exact
                            render={props => <AddDataset {...props} catalog={catalog.slug} />}
                        />
                        <Route component={NotFound} />
                    </Switch>
                </Body>
            </DashboardPage>
        );
    }
}

export default withNotifications(Catalog);
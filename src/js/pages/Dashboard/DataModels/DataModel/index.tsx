import React, { Component } from 'react';
import { toast } from 'react-toastify';
import ToastContent from '../../../../components/ToastContent';
import { LoadingOverlay } from '@castoredc/matter';
import { Route, Switch } from 'react-router-dom';
import DocumentTitle from 'components/DocumentTitle';
import Header from 'components/Layout/Dashboard/Header';
import Body from 'components/Layout/Dashboard/Body';
import SideBar from 'components/SideBar';
import NotFound from 'pages/ErrorPages/NotFound';
import ImportExport from 'pages/Dashboard/DataModels/DataModel/ImportExport';
import Modules from 'pages/Dashboard/DataModels/DataModel/Modules';
import Nodes from 'pages/Dashboard/DataModels/DataModel/Nodes';
import Prefixes from 'pages/Dashboard/DataModels/DataModel/Prefixes';
import Versions from 'pages/Dashboard/DataModels/DataModel/Versions';
import Preview from 'pages/Dashboard/DataModels/DataModel/Preview';
import DataModelForm from 'components/Form/Data/DataModelForm';
import { AuthorizedRouteComponentProps } from 'components/Route';
import { isGranted } from 'utils/PermissionHelper';
import PermissionEditor from 'components/PermissionEditor';
import NoPermission from 'pages/ErrorPages/NoPermission';
import { apiClient } from 'src/js/network';
import {Edit, Manage, View} from "components/PermissionEditor/Permissions";

interface DataModelProps extends AuthorizedRouteComponentProps {}

interface DataModelState {
    dataModel: any;
    isLoading: boolean;
    versions: any;
    currentVersion: any;
    modules: any;
    nodes: any;
    prefixes: any;
}

export default class DataModel extends Component<DataModelProps, DataModelState> {
    constructor(props) {
        super(props);

        this.state = {
            dataModel: null,
            isLoading: true,
            versions: [],
            currentVersion: null,
            modules: [],
            nodes: null,
            prefixes: [],
        };
    }

    componentDidMount() {
        this.getDataModel();
    }

    componentDidUpdate(prevProps) {
        const { match } = this.props;

        if (match.params.version !== prevProps.match.params.version) {
            this.getDataModel();
        }
    }

    getDataModel = (callback = () => {}) => {
        const { match } = this.props;

        this.setState({
            isLoading: true,
        });

        apiClient
            .get('/api/model/' + match.params.model)
            .then(response => {
                const versions = response.data.versions.map(version => {
                    return { value: version.id, label: version.version };
                });

                const currentVersion =
                    match.params.version && !['versions', 'permissions'].includes(match.params.version)
                        ? match.params.version
                        : versions.slice(-1)[0].label;

                this.setState(
                    {
                        dataModel: response.data,
                        isLoading: false,
                        versions: versions,
                        currentVersion: versions.find(({ label }) => label === currentVersion),
                    },
                    () => {
                        callback();
                        this.getModules();
                        this.getNodes();
                        this.getPrefixes();
                    }
                );
            })
            .catch(error => {
                this.setState({
                    isLoading: false,
                });

                const message =
                    error.response && typeof error.response.data.error !== 'undefined'
                        ? error.response.data.error
                        : 'An error occurred while loading the data model';
                toast.error(<ToastContent type="error" message={message} />);
            });
    };

    handleVersionChange = version => {
        const { currentVersion } = this.state;
        const { history } = this.props;

        const newVersion = version.label;
        const newUrl = window.location.pathname.replace('/' + currentVersion.label + '/', '/' + newVersion + '/');

        if (window.location.pathname !== newUrl) {
            history.push(newUrl);
        } else {
            this.setState({
                currentVersion: version,
            });
        }
    };

    getModules = () => {
        const { dataModel, currentVersion } = this.state;

        this.setState({ isLoading: true });

        apiClient
            .get('/api/model/' + dataModel.id + '/v/' + currentVersion.value + '/module')
            .then(response => {
                this.setState({
                    modules: response.data,
                    isLoading: false,
                });
            })
            .catch(error => {
                if (error.response && typeof error.response.data.error !== 'undefined') {
                    toast.error(<ToastContent type="error" message={error.response.data.error} />);
                } else {
                    toast.error(<ToastContent type="error" message="An error occurred" />);
                }

                this.setState({
                    isLoading: false,
                });
            });
    };

    getNodes = () => {
        const { dataModel, currentVersion } = this.state;

        this.setState({ isLoading: true });

        apiClient
            .get('/api/model/' + dataModel.id + '/v/' + currentVersion.value + '/node')
            .then(response => {
                this.setState({
                    nodes: response.data,
                    isLoading: false,
                });
            })
            .catch(error => {
                this.setState({
                    isLoading: false,
                });

                const message =
                    error.response && typeof error.response.data.error !== 'undefined'
                        ? error.response.data.error
                        : 'An error occurred while loading the nodes';
                toast.error(<ToastContent type="error" message={message} />);
            });
    };

    getPrefixes = () => {
        const { dataModel, currentVersion } = this.state;

        this.setState({ isLoading: true });

        apiClient
            .get('/api/model/' + dataModel.id + '/v/' + currentVersion.value + '/prefix')
            .then(response => {
                this.setState({
                    prefixes: response.data,
                    isLoading: false,
                });
            })
            .catch(error => {
                this.setState({
                    isLoading: false,
                });

                const message =
                    error.response && typeof error.response.data.error !== 'undefined'
                        ? error.response.data.error
                        : 'An error occurred while loading the prefixes';
                toast.error(<ToastContent type="error" message={message} />);
            });
    };

    render() {
        const { history, location, user } = this.props;
        const { isLoading, dataModel, versions, currentVersion, modules, nodes, prefixes } = this.state;

        if (isLoading) {
            return <LoadingOverlay accessibleLabel="Loading data model" />;
        }

        if (!isGranted('edit', dataModel.permissions)) {
            return <NoPermission text="You do not have permission to edit this data model" />;
        }

        return (
            <>
                <DocumentTitle title={dataModel.title} />

                <SideBar
                    back={{
                        to: '/dashboard/data-models',
                        title: 'Back to data models',
                    }}
                    location={location}
                    onVersionChange={this.handleVersionChange}
                    items={[
                        {
                            to: '/dashboard/data-models/' + dataModel.id,
                            exact: true,
                            title: 'Data model',
                            icon: 'structure',
                        },
                        {
                            to: '/dashboard/data-models/' + dataModel.id + '/versions',
                            exact: true,
                            title: 'Versions',
                            customIcon: 'versions',
                        },
                        ...(isGranted('manage', dataModel.permissions)
                            ? [
                                  {
                                      to: '/dashboard/data-models/' + dataModel.id + '/permissions',
                                      exact: true,
                                      title: 'Permissions',
                                      icon: 'usersLight',
                                  },
                              ]
                            : []),
                        {
                            type: 'separator',
                        },
                        {
                            type: 'version',
                            current: currentVersion,
                            versions: versions,
                        },
                        {
                            type: 'separator',
                        },
                        {
                            to: '/dashboard/data-models/' + dataModel.id + '/' + currentVersion.label + '/modules',
                            exact: true,
                            title: 'Groups',
                            customIcon: 'modules',
                        },
                        {
                            to: '/dashboard/data-models/' + dataModel.id + '/' + currentVersion.label + '/nodes/internal',
                            exact: true,
                            title: 'Nodes',
                            customIcon: 'node',
                        },
                        {
                            to: '/dashboard/data-models/' + dataModel.id + '/' + currentVersion.label + '/prefixes',
                            exact: true,
                            title: 'Prefixes',
                            customIcon: 'prefix',
                        },
                        {
                            type: 'separator',
                        },
                        {
                            to: '/dashboard/data-models/' + dataModel.id + '/' + currentVersion.label + '/import-export',
                            exact: true,
                            title: 'Import/export',
                            icon: 'upload',
                        },
                        {
                            type: 'separator',
                        },
                        {
                            to: '/dashboard/data-models/' + dataModel.id + '/' + currentVersion.label + '/preview',
                            exact: true,
                            title: 'Preview',
                            customIcon: 'preview',
                        },
                    ]}
                />
                <Body fullWidth={true}>
                    <Header title={dataModel.title} />

                    <Switch>
                        <Route
                            path="/dashboard/data-models/:model"
                            exact
                            render={props => <DataModelForm history={history} dataModel={dataModel} />}
                        />
                        <Route
                            path="/dashboard/data-models/:model/versions"
                            exact
                            render={props => <Versions getDataModel={this.getDataModel} dataModel={dataModel} user={user} {...props} />}
                        />
                        <Route
                            path="/dashboard/data-models/:model/permissions"
                            exact
                            render={props =>
                                isGranted('manage', dataModel.permissions) ? (
                                    <PermissionEditor
                                        getObject={this.getDataModel}
                                        type="model"
                                        object={dataModel}
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
                            path="/dashboard/data-models/:model/:version/modules"
                            exact
                            render={props => (
                                <Modules
                                    modules={modules}
                                    nodes={nodes}
                                    prefixes={prefixes}
                                    getModules={this.getModules}
                                    dataModel={dataModel}
                                    version={currentVersion.value}
                                    user={user}
                                    {...props}
                                />
                            )}
                        />
                        <Route
                            path="/dashboard/data-models/:model/:version/nodes/:nodeType"
                            exact
                            render={props => (
                                <Nodes nodes={nodes} getNodes={this.getNodes} dataModel={dataModel} version={currentVersion} user={user} {...props} />
                            )}
                        />
                        <Route
                            path="/dashboard/data-models/:model/:version/prefixes"
                            exact
                            render={props => (
                                <Prefixes
                                    prefixes={prefixes}
                                    getPrefixes={this.getPrefixes}
                                    dataModel={dataModel}
                                    version={currentVersion.value}
                                    user={user}
                                    {...props}
                                />
                            )}
                        />
                        <Route
                            path="/dashboard/data-models/:model/:version/preview"
                            exact
                            render={props => <Preview dataModel={dataModel} version={currentVersion.value} user={user} {...props} />}
                        />
                        <Route
                            path="/dashboard/data-models/:model/:version/import-export"
                            exact
                            render={props => (
                                <ImportExport
                                    dataModel={dataModel}
                                    version={currentVersion.value}
                                    getDataModel={this.getDataModel}
                                    user={user}
                                    {...props}
                                />
                            )}
                        />

                        <Route component={NotFound} />
                    </Switch>
                </Body>
            </>
        );
    }
}

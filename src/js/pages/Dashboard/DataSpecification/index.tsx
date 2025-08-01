import React, { Component } from 'react';
import LoadingOverlay from 'components/LoadingOverlay';
import { Redirect, Route, Switch } from 'react-router-dom';
import DocumentTitle from 'components/DocumentTitle';
import Header from 'components/Layout/Dashboard/Header';
import Body from 'components/Layout/Dashboard/Body';
import SideBar from 'components/SideBar';
import NotFound from 'pages/ErrorPages/NotFound';
import ImportExport from 'pages/Dashboard/DataSpecification/ImportExport';
import Modules from 'pages/Dashboard/DataSpecification/Modules';
import Nodes from 'pages/Dashboard/DataSpecification/Nodes';
import Prefixes from 'pages/Dashboard/DataSpecification/Prefixes';
import Versions from 'pages/Dashboard/DataSpecification/Versions';
import Preview from 'pages/Dashboard/DataSpecification/Preview';
import { AuthorizedRouteComponentProps } from 'components/Route';
import { isGranted } from 'utils/PermissionHelper';
import PermissionEditor from 'components/PermissionEditor';
import NoPermission from 'pages/ErrorPages/NoPermission';
import { apiClient } from '../../../network';
import { Edit, Manage, View } from 'components/PermissionEditor/Permissions';
import DataSpecificationDetails from 'pages/Dashboard/DataSpecification/DataSpecificationDetails';
import { getType, ucfirst } from '../../../util';
import OptionGroups from 'pages/Dashboard/DataSpecification/OptionGroups';
import Forms from 'pages/Dashboard/DataSpecification/Forms';
import Display from 'pages/Dashboard/DataSpecification/Display';
import { Types } from 'types/Types';
import DashboardPage from 'components/Layout/Dashboard/DashboardPage';
import withNotifications, { ComponentWithNotifications } from 'components/WithNotifications';
import GroupsIcon from '@mui/icons-material/Groups';
import AccountTreeIcon from '@mui/icons-material/AccountTree';
import ContentCopyIcon from '@mui/icons-material/ContentCopy';
import WidgetsIcon from '@mui/icons-material/Widgets';
import LinkIcon from '@mui/icons-material/Link';
import RadioButtonCheckedIcon from '@mui/icons-material/RadioButtonChecked';
import AssignmentIcon from '@mui/icons-material/Assignment';
import ListIcon from '@mui/icons-material/List';
import UploadIcon from '@mui/icons-material/Upload';
import VisibilityIcon from '@mui/icons-material/Visibility';
import PolylineIcon from '@mui/icons-material/Polyline';

interface DataSpecificationProps extends AuthorizedRouteComponentProps, ComponentWithNotifications {
    type: string;
}

interface DataSpecificationState {
    dataSpecification: any;
    isLoading: boolean;
    versions: any;
    currentVersion: any;
    modules: any;
    forms: any;
    displaySettings: any;
    nodes: any;
    prefixes: any;
    optionGroups: any;
    types: Types;
}

class DataSpecification extends Component<DataSpecificationProps, DataSpecificationState> {
    constructor(props) {
        super(props);

        this.state = {
            dataSpecification: null,
            isLoading: true,
            versions: [],
            currentVersion: null,
            modules: [],
            forms: [],
            displaySettings: [],
            nodes: null,
            prefixes: [],
            optionGroups: [],
            types: {
                fieldTypes: {
                    plain: {},
                    annotated: [],
                },
                dataTypes: [],
                displayTypes: {
                    plain: {},
                    annotated: [],
                },
            },
        };
    }

    componentDidMount() {
        this.getDataSpecification();
    }

    componentDidUpdate(prevProps) {
        const { match } = this.props;

        if (match.params.version !== prevProps.match.params.version) {
            this.getDataSpecification();
        }
    }

    getDataSpecification = (callback = () => {
    }) => {
        const { type, match, notifications } = this.props;

        this.setState({
            isLoading: true,
        });

        apiClient
            .get('/api/' + type + '/' + match.params.model)
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
                        dataSpecification: response.data,
                        isLoading: false,
                        versions: versions,
                        currentVersion: versions.find(({ label }) => label === currentVersion),
                    },
                    () => {
                        callback();
                        this.getModules();
                        this.getNodes();
                        this.getPrefixes();
                        if (type === 'metadata-model') {
                            this.getForms();
                            this.getOptionGroups();
                            this.getDisplaySettings();
                        }
                        this.getTypes();
                    },
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
                notifications.show(message, { variant: 'error' });
            });
    };

    handleVersionChange = (versionId) => {
        const { currentVersion, versions } = this.state;
        const { history } = this.props;
        const selectedVersion = versions.find(version => version.value === versionId);

        const newVersion = selectedVersion.label;
        const newUrl = window.location.pathname.replace('/' + currentVersion.label + '/', '/' + newVersion + '/');

        if (window.location.pathname !== newUrl) {
            history.push(newUrl);
        } else {
            this.setState({
                currentVersion: selectedVersion,
            });
        }
    };

    getModules = () => {
        const { dataSpecification, currentVersion } = this.state;
        const { type, notifications } = this.props;

        this.setState({ isLoading: true });

        apiClient
            .get('/api/' + type + '/' + dataSpecification.id + '/v/' + currentVersion.value + '/module')
            .then(response => {
                this.setState({
                    modules: response.data,
                    isLoading: false,
                });
            })
            .catch(error => {
                if (error.response && typeof error.response.data.error !== 'undefined') {
                    notifications.show(error.response.data.error, { variant: 'error' });
                } else {
                    notifications.show('An error occurred', { variant: 'error' });
                }

                this.setState({
                    isLoading: false,
                });
            });
    };

    getForms = () => {
        const { dataSpecification, currentVersion } = this.state;
        const { type, notifications } = this.props;

        this.setState({ isLoading: true });

        apiClient
            .get('/api/' + type + '/' + dataSpecification.id + '/v/' + currentVersion.value + '/form')
            .then(response => {
                this.setState({
                    forms: response.data,
                    isLoading: false,
                });
            })
            .catch(error => {
                if (error.response && typeof error.response.data.error !== 'undefined') {
                    notifications.show(error.response.data.error, { variant: 'error' });
                } else {
                    notifications.show('An error occurred', { variant: 'error' });
                }

                this.setState({
                    isLoading: false,
                });
            });
    };

    getDisplaySettings = () => {
        const { dataSpecification, currentVersion } = this.state;
        const { type, notifications } = this.props;

        this.setState({ isLoading: true });

        apiClient
            .get('/api/' + type + '/' + dataSpecification.id + '/v/' + currentVersion.value + '/display')
            .then(response => {
                this.setState({
                    displaySettings: response.data,
                    isLoading: false,
                });
            })
            .catch(error => {
                if (error.response && typeof error.response.data.error !== 'undefined') {
                    notifications.show(error.response.data.error, { variant: 'error' });
                } else {
                    notifications.show('An error occurred', { variant: 'error' });
                }

                this.setState({
                    isLoading: false,
                });
            });
    };

    getNodes = () => {
        const { dataSpecification, currentVersion } = this.state;
        const { type, notifications } = this.props;

        this.setState({ isLoading: true });

        apiClient
            .get('/api/' + type + '/' + dataSpecification.id + '/v/' + currentVersion.value + '/node')
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
                notifications.show(message, { variant: 'error' });
            });
    };

    getPrefixes = () => {
        const { dataSpecification, currentVersion } = this.state;
        const { type, notifications } = this.props;

        this.setState({ isLoading: true });

        apiClient
            .get('/api/' + type + '/' + dataSpecification.id + '/v/' + currentVersion.value + '/prefix')
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
                notifications.show(message, { variant: 'error' });
            });
    };

    getOptionGroups = () => {
        const { dataSpecification, currentVersion } = this.state;
        const { type, notifications } = this.props;

        this.setState({ isLoading: true });

        apiClient
            .get('/api/' + type + '/' + dataSpecification.id + '/v/' + currentVersion.value + '/option-group')
            .then(response => {
                this.setState({
                    optionGroups: response.data,
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
                        : 'An error occurred while loading the option groups';
                notifications.show(message, { variant: 'error' });
            });
    };

    getTypes = () => {
        const { dataSpecification, currentVersion } = this.state;
        const { type, notifications } = this.props;

        this.setState({ isLoading: true });

        apiClient
            .get('/api/' + type + '/' + dataSpecification.id + '/v/' + currentVersion.value + '/types')
            .then(response => {
                this.setState({
                    types: response.data,
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
                        : 'An error occurred while loading the types';
                notifications.show(message, { variant: 'error' });
            });
    };

    render() {
        const { type, history, location, user } = this.props;
        const {
            isLoading,
            dataSpecification,
            versions,
            currentVersion,
            modules,
            forms,
            displaySettings,
            nodes,
            prefixes,
            optionGroups,
            types,
        } =
            this.state;

        if (isLoading) {
            return <LoadingOverlay accessibleLabel="Loading data model" />;
        }

        if (!isGranted('edit', dataSpecification.permissions)) {
            return <NoPermission text="You do not have permission to edit this data model" />;
        }

        const mainUrl = type === 'data-model' ? '/dashboard/data-models' : '/dashboard/metadata-models';

        return (
            <DashboardPage>
                <DocumentTitle title={dataSpecification.title} />

                <SideBar
                    back={{
                        to: mainUrl,
                        title: 'Back to ' + getType(type) + 's',
                    }}
                    location={location}
                    onVersionChange={this.handleVersionChange}
                    items={[
                        {
                            to: mainUrl + '/' + dataSpecification.id,
                            exact: true,
                            title: ucfirst(getType(type)),
                            icon: <AccountTreeIcon />,
                        },
                        {
                            to: mainUrl + '/' + dataSpecification.id + '/versions',
                            exact: true,
                            title: 'Versions',
                            icon: <ContentCopyIcon />,
                        },
                        ...(isGranted('manage', dataSpecification.permissions)
                            ? [
                                {
                                    to: mainUrl + '/' + dataSpecification.id + '/permissions',
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
                            type: 'version',
                            current: currentVersion,
                            versions: versions,
                        },
                        {
                            type: 'separator',
                        },
                        {
                            to: mainUrl + '/' + dataSpecification.id + '/' + currentVersion.label + '/modules',
                            title: 'Groups',
                            icon: <WidgetsIcon />,
                        },
                        {
                            to: mainUrl + '/' + dataSpecification.id + '/' + currentVersion.label + '/nodes/internal',
                            title: 'Nodes',
                            icon: <PolylineIcon />,
                        },
                        {
                            to: mainUrl + '/' + dataSpecification.id + '/' + currentVersion.label + '/prefixes',
                            exact: true,
                            title: 'Prefixes',
                            icon: <LinkIcon />,
                        },
                        {
                            type: 'separator',
                        },
                        ...(type === 'metadata-model'
                            ? [
                                {
                                    to: mainUrl + '/' + dataSpecification.id + '/' + currentVersion.label + '/option-group',
                                    exact: true,
                                    title: 'Option groups',
                                    icon: <RadioButtonCheckedIcon />,
                                },
                                {
                                    to: mainUrl + '/' + dataSpecification.id + '/' + currentVersion.label + '/forms',
                                    title: 'Forms',
                                    icon: <AssignmentIcon />,
                                },
                                {
                                    to: mainUrl + '/' + dataSpecification.id + '/' + currentVersion.label + '/display',
                                    title: 'Display',
                                    icon: <ListIcon />,
                                },
                                {
                                    type: 'separator',
                                },
                            ]
                            : []),
                        {
                            to: mainUrl + '/' + dataSpecification.id + '/' + currentVersion.label + '/import-export',
                            exact: true,
                            title: 'Import/export',
                            icon: <UploadIcon />,
                        },
                        {
                            type: 'separator',
                        },
                        {
                            to: mainUrl + '/' + dataSpecification.id + '/' + currentVersion.label + '/preview',
                            exact: true,
                            title: 'Preview',
                            icon: <VisibilityIcon />,
                        },
                    ]}
                    history={history}
                    user={user}
                />
                <Body>
                    <Header title={dataSpecification.title} />

                    <Switch>
                        <Route
                            path={['/dashboard/data-models/:model', '/dashboard/metadata-models/:model']}
                            exact
                            render={props => <DataSpecificationDetails type={type} dataSpecification={dataSpecification}
                                                                       user={user} {...props} />}
                        />
                        <Route
                            path={['/dashboard/data-models/:model/versions', '/dashboard/metadata-models/:model/versions']}
                            exact
                            render={props => (
                                <Versions
                                    type={type}
                                    getDataSpecification={this.getDataSpecification}
                                    dataSpecification={dataSpecification}
                                    user={user}
                                    {...props}
                                />
                            )}
                        />
                        <Route
                            path={['/dashboard/data-models/:model/permissions', '/dashboard/metadata-models/:model/permissions']}
                            exact
                            render={props =>
                                isGranted('manage', dataSpecification.permissions) ? (
                                    <PermissionEditor
                                        getObject={this.getDataSpecification}
                                        type={type}
                                        object={dataSpecification}
                                        user={user}
                                        permissions={[View, Edit, Manage]}
                                        {...props}
                                    />
                                ) : (
                                    <NoPermission text="You do not have access to this page" />
                                )
                            }
                        />
                        {type === 'metadata-model' && (
                            <Route
                                path="/dashboard/metadata-models/:model/:version/forms/:formId?"
                                exact
                                render={props => (
                                    <Forms
                                        type={type}
                                        nodes={nodes}
                                        forms={forms}
                                        getForms={this.getForms}
                                        dataSpecification={dataSpecification}
                                        version={currentVersion.value}
                                        user={user}
                                        types={types}
                                        optionGroups={optionGroups}
                                        {...props}
                                    />
                                )}
                            />
                        )}
                        {type === 'metadata-model' && (
                            <Route
                                path="/dashboard/metadata-models/:model/:version/option-group"
                                exact
                                render={props => (
                                    <OptionGroups
                                        type={type}
                                        optionGroups={optionGroups}
                                        getOptionGroups={this.getOptionGroups}
                                        dataSpecification={dataSpecification}
                                        version={currentVersion.value}
                                        user={user}
                                        {...props}
                                    />
                                )}
                            />
                        )}
                        {type === 'metadata-model' && (
                            <Redirect
                                exact
                                from="/dashboard/metadata-models/:model/:version/display"
                                to="/dashboard/metadata-models/:model/:version/display/fdp"
                            />
                        )}
                        {type === 'metadata-model' && (
                            <Route
                                path="/dashboard/metadata-models/:model/:version/display/:resourceType?"
                                exact
                                render={props => (
                                    <Display
                                        type={type}
                                        displaySettings={displaySettings}
                                        getDisplaySettings={this.getDisplaySettings}
                                        nodes={nodes}
                                        dataSpecification={dataSpecification}
                                        version={currentVersion}
                                        user={user}
                                        types={types}
                                        {...props}
                                    />
                                )}
                            />
                        )}
                        <Route
                            path={[
                                '/dashboard/data-models/:model/:version/modules/:moduleId?',
                                '/dashboard/metadata-models/:model/:version/modules/:moduleId?',
                            ]}
                            exact
                            render={props => (
                                <Modules
                                    type={type}
                                    modules={modules}
                                    nodes={nodes}
                                    prefixes={prefixes}
                                    getModules={this.getModules}
                                    dataSpecification={dataSpecification}
                                    version={currentVersion.value}
                                    user={user}
                                    {...props}
                                />
                            )}
                        />
                        <Route
                            path={[
                                '/dashboard/data-models/:model/:version/nodes/:nodeType?',
                                '/dashboard/metadata-models/:model/:version/nodes/:nodeType?',
                            ]}
                            exact
                            render={props => (
                                <Nodes
                                    type={type}
                                    nodes={nodes}
                                    getNodes={this.getNodes}
                                    dataSpecification={dataSpecification}
                                    version={currentVersion}
                                    user={user}
                                    types={types}
                                    optionGroups={optionGroups}
                                    prefixes={prefixes}
                                    {...props}
                                />
                            )}
                        />
                        <Route
                            path={['/dashboard/data-models/:model/:version/prefixes', '/dashboard/metadata-models/:model/:version/prefixes']}
                            exact
                            render={props => (
                                <Prefixes
                                    type={type}
                                    prefixes={prefixes}
                                    getPrefixes={this.getPrefixes}
                                    dataSpecification={dataSpecification}
                                    version={currentVersion.value}
                                    user={user}
                                    {...props}
                                />
                            )}
                        />
                        <Route
                            path={['/dashboard/data-models/:model/:version/preview', '/dashboard/metadata-models/:model/:version/preview']}
                            exact
                            render={props => (
                                <Preview type={type} dataSpecification={dataSpecification}
                                         version={currentVersion.value} user={user} {...props} />
                            )}
                        />
                        <Route
                            path={[
                                '/dashboard/data-models/:model/:version/import-export',
                                '/dashboard/metadata-models/:model/:version/import-export',
                            ]}
                            exact
                            render={props => (
                                <ImportExport
                                    type={type}
                                    dataSpecification={dataSpecification}
                                    version={currentVersion.value}
                                    getDataSpecification={this.getDataSpecification}
                                    user={user}
                                    {...props}
                                />
                            )}
                        />

                        <Route component={NotFound} />
                    </Switch>
                </Body>
            </DashboardPage>
        );
    }
}

export default withNotifications(DataSpecification);
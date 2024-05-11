import React, { Component } from 'react';
import { toast } from 'react-toastify';
import ToastItem from 'components/ToastItem';
import { LoadingOverlay } from '@castoredc/matter';
import { Route, Switch } from 'react-router-dom';
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

interface DataSpecificationProps extends AuthorizedRouteComponentProps {
    type: string;
}

interface DataSpecificationState {
    dataSpecification: any;
    isLoading: boolean;
    versions: any;
    currentVersion: any;
    modules: any;
    forms: any;
    nodes: any;
    prefixes: any;
    optionGroups: any;
    types: {
        fieldTypes: {
            plain: {
                [key: string]: {
                    value: string,
                    label: string
                }[],
            },
            annotated: {
                value: string,
                label: string
            }[]
        },
        dataTypes: {
            value: string,
            label: string
        }[],
    };
}

export default class DataSpecification extends Component<DataSpecificationProps, DataSpecificationState> {
    constructor(props) {
        super(props);

        this.state = {
            dataSpecification: null,
            isLoading: true,
            versions: [],
            currentVersion: null,
            modules: [],
            forms: [],
            nodes: null,
            prefixes: [],
            optionGroups: [],
            types: {
                fieldTypes: {
                    plain: {},
                    annotated: [],
                },
                dataTypes: [],
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
        const { type, match } = this.props;

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
                toast.error(<ToastItem type="error" title={message} />);
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
        const { dataSpecification, currentVersion } = this.state;
        const { type } = this.props;

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
                    toast.error(<ToastItem type="error" title={error.response.data.error} />);
                } else {
                    toast.error(<ToastItem type="error" title="An error occurred" />);
                }

                this.setState({
                    isLoading: false,
                });
            });
    };

    getForms = () => {
        const { dataSpecification, currentVersion } = this.state;
        const { type } = this.props;

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
                    toast.error(<ToastItem type="error" title={error.response.data.error} />);
                } else {
                    toast.error(<ToastItem type="error" title="An error occurred" />);
                }

                this.setState({
                    isLoading: false,
                });
            });
    };

    getNodes = () => {
        const { dataSpecification, currentVersion } = this.state;
        const { type } = this.props;

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
                toast.error(<ToastItem type="error" title={message} />);
            });
    };

    getPrefixes = () => {
        const { dataSpecification, currentVersion } = this.state;
        const { type } = this.props;

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
                toast.error(<ToastItem type="error" title={message} />);
            });
    };

    getOptionGroups = () => {
        const { dataSpecification, currentVersion } = this.state;
        const { type } = this.props;

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
                toast.error(<ToastItem type="error" title={message} />);
            });
    };

    getTypes = () => {
        const { dataSpecification, currentVersion } = this.state;
        const { type } = this.props;

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
                toast.error(<ToastItem type="error" title={message} />);
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
            nodes,
            prefixes,
            optionGroups,
            types,
        } = this.state;

        if (isLoading) {
            return <LoadingOverlay accessibleLabel="Loading data model" />;
        }

        if (!isGranted('edit', dataSpecification.permissions)) {
            return <NoPermission text="You do not have permission to edit this data model" />;
        }

        const mainUrl = type === 'data-model' ? '/dashboard/data-models' : '/dashboard/metadata-models';

        return (
            <>
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
                            icon: 'structure',
                        },
                        {
                            to: mainUrl + '/' + dataSpecification.id + '/versions',
                            exact: true,
                            title: 'Versions',
                            customIcon: 'versions',
                        },
                        ...(isGranted('manage', dataSpecification.permissions)
                            ? [
                                {
                                    to: mainUrl + '/' + dataSpecification.id + '/permissions',
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
                            to: mainUrl + '/' + dataSpecification.id + '/' + currentVersion.label + '/modules',
                            exact: true,
                            title: 'Groups',
                            customIcon: 'modules',
                        },
                        {
                            to: mainUrl + '/' + dataSpecification.id + '/' + currentVersion.label + '/nodes/internal',
                            exact: true,
                            title: 'Nodes',
                            customIcon: 'node',
                        },
                        {
                            to: mainUrl + '/' + dataSpecification.id + '/' + currentVersion.label + '/prefixes',
                            exact: true,
                            title: 'Prefixes',
                            customIcon: 'prefix',
                        },
                        {
                            type: 'separator',
                        },
                        ...(type === 'metadata-model' ? [
                            {
                                to: mainUrl + '/' + dataSpecification.id + '/' + currentVersion.label + '/option-group',
                                exact: true,
                                title: 'Option groups',
                                icon: 'radioOptions',
                            },
                            {
                                to: mainUrl + '/' + dataSpecification.id + '/' + currentVersion.label + '/forms',
                                exact: true,
                                title: 'Forms',
                                icon: 'survey',
                            },
                            {
                                type: 'separator',
                            },
                        ] : []),
                        {
                            to: mainUrl + '/' + dataSpecification.id + '/' + currentVersion.label + '/import-export',
                            exact: true,
                            title: 'Import/export',
                            icon: 'upload',
                        },
                        {
                            type: 'separator',
                        },
                        {
                            to: mainUrl + '/' + dataSpecification.id + '/' + currentVersion.label + '/preview',
                            exact: true,
                            title: 'Preview',
                            customIcon: 'preview',
                        },
                    ]}
                />
                <Body>
                    <Header title={dataSpecification.title} />

                    <Switch>
                        <Route
                            path={[
                                '/dashboard/data-models/:model',
                                '/dashboard/metadata-models/:model',
                            ]}
                            exact
                            render={props => <DataSpecificationDetails
                                type={type}
                                dataSpecification={dataSpecification}
                                user={user}
                                {...props}
                            />}
                        />
                        <Route
                            path={[
                                '/dashboard/data-models/:model/versions',
                                '/dashboard/metadata-models/:model/versions',
                            ]}
                            exact
                            render={props => <Versions
                                type={type}
                                getDataSpecification={this.getDataSpecification}
                                dataSpecification={dataSpecification}
                                user={user}
                                {...props}
                            />}
                        />
                        <Route
                            path={[
                                '/dashboard/data-models/:model/permissions',
                                '/dashboard/metadata-models/:model/permissions',
                            ]}
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
                        {type === 'metadata-model' && <Route
                                path="/dashboard/metadata-models/:model/:version/forms"
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
                        }
                        {type === 'metadata-model' && <Route
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
                        }
                        <Route
                            path={[
                                '/dashboard/data-models/:model/:version/modules',
                                '/dashboard/metadata-models/:model/:version/modules',
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
                                '/dashboard/data-models/:model/:version/nodes/:nodeType',
                                '/dashboard/metadata-models/:model/:version/nodes/:nodeType',
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
                                    {...props}
                                />
                            )}
                        />
                        <Route
                            path={[
                                '/dashboard/data-models/:model/:version/prefixes',
                                '/dashboard/metadata-models/:model/:version/prefixes',
                            ]}
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
                            path={[
                                '/dashboard/data-models/:model/:version/preview',
                                '/dashboard/metadata-models/:model/:version/preview',
                            ]}
                            exact
                            render={props => <Preview
                                type={type}
                                dataSpecification={dataSpecification}
                                version={currentVersion.value}
                                user={user}
                                {...props}
                            />}
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
            </>
        );
    }
}

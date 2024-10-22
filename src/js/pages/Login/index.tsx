import React, { Component } from 'react';
import DocumentTitle from '../../components/DocumentTitle';
import './Login.scss';
import queryString from 'query-string';
import { toast } from 'react-toastify';
import ToastItem from 'components/ToastItem';
import { CastorLogo, LoadingOverlay } from '@castoredc/matter';
import LoginForm from '../../components/Form/LoginForm';
import { apiClient } from 'src/js/network';
import { Catalog } from 'types/Resources';
import { CatalogBrandType } from 'types/CatalogType';
import { ServerType } from 'types/ServerType';

interface LoginProps {
    match: {
        params: {
            catalogSlug?: string;
        };
    };
    location: {
        search: string;
    };
}

interface LoginState {
    servers: ServerType[];
    catalog?: CatalogBrandType;
    isLoading: boolean;
    selectedServer?: string;
    serverLocked: boolean;
    view: string;
}

export default class Login extends Component<LoginProps, LoginState> {
    constructor(props: LoginProps) {
        super(props);

        this.state = {
            servers: [],
            catalog: undefined,
            isLoading: true,
            selectedServer: undefined,
            serverLocked: true,
            view: 'generic',
        };
    }

    componentDidMount() {
        const { catalogSlug } = this.props.match.params;

        if (typeof catalogSlug !== 'undefined') {
            this.getCatalog(catalogSlug, () => {
                this.getServers();
            });
        } else {
            this.getServers();
        }
    }

    getServers = () => {
        apiClient
            .get('/api/castor/servers')
            .then((response) => {
                const params = queryString.parse(this.props.location.search);
                const defaultServer = response.data.find((server: ServerType) => server.default)?.id;
                const serverIds = response.data.map((server: ServerType) => server.id);
                const urlParamServer = typeof params.server !== 'undefined' ? parseInt(params.server as string) : null;
                const serverLocked = typeof params.serverLocked !== 'undefined' ? params.serverLocked === '1' : false;
                const view = typeof params.view !== 'undefined' ? (params.view as string) : 'generic';
                const selectedServer = urlParamServer !== null && serverIds.includes(urlParamServer) ? urlParamServer : defaultServer || null;

                this.setState({
                    servers: response.data,
                    isLoading: false,
                    selectedServer: selectedServer,
                    serverLocked: serverLocked,
                    view: view,
                });
            })
            .catch((error) => {
                this.setState({
                    isLoading: false,
                });
                toast.error(<ToastItem type="error" title="An error occurred" />);
            });
    };

    getCatalog = (catalog: string, callback?: () => void) => {
        apiClient
            .get(`/api/brand/${catalog}`)
            .then((response) => {
                if (typeof callback === 'function') {
                    this.setState(
                        {
                            catalog: response.data,
                        },
                        callback
                    );
                } else {
                    this.setState({
                        catalog: response.data,
                        isLoading: false,
                    });
                }
            })
            .catch((error) => {
                this.setState({
                    isLoading: false,
                });
                toast.error(<ToastItem type="error" title="An error occurred" />);
            });
    };

    render() {
        const { serverLocked, view, servers, selectedServer, catalog, isLoading } = this.state;
        const params = queryString.parse(this.props.location.search);
        const path = typeof params.path !== 'undefined' ? (params.path as string) : '';

        if (isLoading) {
            return <LoadingOverlay accessibleLabel="Loading" />;
        }

        return (
            <div className="Login TopLevelContainer">
                <DocumentTitle title="FAIR Data Point | Log in" />

                <div className="Skip" />

                <div className="LoginContainer">
                    <div className="LoginLogo">
                        <CastorLogo className="Logo" />
                    </div>

                    <LoginForm
                        path={path}
                        selectedServerId={selectedServer}
                        serverLocked={serverLocked}
                        servers={servers}
                        catalog={catalog}
                        view={view}
                    />
                </div>
            </div>
        );
    }
}
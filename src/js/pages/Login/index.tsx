import React, { Component } from 'react';
import DocumentTitle from '../../components/DocumentTitle';
import LoginContainer from '../../components/Login/LoginContainer';
import queryString from 'query-string';
import LoginForm from '../../components/Form/LoginForm';
import { apiClient } from 'src/js/network';
import { CatalogBrandType } from 'types/CatalogType';
import { ServerType } from 'types/ServerType';
import LoadingOverlay from 'components/LoadingOverlay';
import withNotifications, { ComponentWithNotifications } from 'components/WithNotifications';
import Container from '@mui/material/Container';
import Logo from 'components/Logo';

interface LoginProps extends ComponentWithNotifications {
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

class Login extends Component<LoginProps, LoginState> {
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
        const { notifications } = this.props;

        apiClient
            .get('/api/castor/servers')
            .then(response => {
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
            .catch(error => {
                this.setState({
                    isLoading: false,
                });
                notifications.show('An error occurred', { variant: 'error' });
            });
    };

    getCatalog = (catalog: string, callback?: () => void) => {
        const { notifications } = this.props;

        apiClient
            .get(`/api/brand/${catalog}`)
            .then(response => {
                if (typeof callback === 'function') {
                    this.setState(
                        {
                            catalog: response.data,
                        },
                        callback,
                    );
                } else {
                    this.setState({
                        catalog: response.data,
                        isLoading: false,
                    });
                }
            })
            .catch(error => {
                this.setState({
                    isLoading: false,
                });
                notifications.show('An error occurred', { variant: 'error' });
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
            <LoginContainer
                logo={<Logo color="#124ea4" />}
            >
                <DocumentTitle title="FAIR Data Point | Log in" />
                <Container maxWidth="sm">
                    <LoginForm
                        path={path}
                        selectedServerId={selectedServer}
                        serverLocked={serverLocked}
                        servers={servers}
                        catalog={catalog}
                        view={view}
                    />
                </Container>
            </LoginContainer>
        );
    }
}

export default withNotifications(Login);
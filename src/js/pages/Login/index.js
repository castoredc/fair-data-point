import React, {Component} from "react";

import DocumentTitle from "../../components/DocumentTitle";
import './Login.scss';
import queryString from 'query-string';
import axios from "axios";
import {toast} from "react-toastify";
import ToastContent from "../../components/ToastContent";
import {CastorLogo, LoadingOverlay} from "@castoredc/matter";
import LoginForm from "../../components/Form/LoginForm";

export default class Login extends Component {
    constructor(props) {
        super(props);

        this.state = {
            servers: [],
            catalog: null,
            isLoading: true,
            selectedServer: null,
            serverLocked: true,
            view: 'generic'
        };
    }

    componentDidMount() {
        if (typeof this.props.match.params.catalogSlug !== 'undefined') {
            this.getCatalog(this.props.match.params.catalogSlug, () => {
                this.getServers();
            });
        } else {
            this.getServers();
        }
    }

    getServers = () => {
        axios.get('/api/castor/servers')
            .then((response) => {
                const params = queryString.parse(this.props.location.search);
                const defaultServer = response.data.filter((server) => server.default)[0].id;
                const serverIds = response.data.map((server) => server.id);
                const urlParamServer = (typeof params.server !== 'undefined') ? parseInt(params.server) : null;
                const serverLocked = (typeof params.serverLocked !== 'undefined') ? (params.serverLocked === '1') : false;
                const view = (typeof params.view !== 'undefined') ? params.view : 'generic';
                const selectedServer = (urlParamServer !== null && serverIds.includes(urlParamServer)) ? urlParamServer : defaultServer;

                this.setState({
                    servers: response.data,
                    isLoading: false,
                    selectedServer: selectedServer,
                    serverLocked: serverLocked,
                    view: view
                });
            })
            .catch((error) => {
                this.setState({
                    isLoading: false,
                });
                toast.error(<ToastContent type="error" message="An error occurred"/>);
            });
    };

    getCatalog = (catalog, callback) => {
        axios.get('/api/brand/' + catalog)
            .then((response) => {
                if (typeof callback === "function") {
                    this.setState({
                        catalog: response.data,
                    }, callback);
                } else {
                    this.setState({
                        catalog: response.data,
                        isLoading: false
                    });
                }
            })
            .catch((error) => {
                this.setState({
                    isLoading: false,
                });
                toast.error(<ToastContent type="error" message="An error occurred"/>);
            });
    };

    render() {
        const {serverLocked, view, servers, selectedServer, catalog, isLoading} = this.state;

        const params = queryString.parse(this.props.location.search);
        const path = typeof params.path !== 'undefined' ? params.path : '';

        if (isLoading) {
            return <LoadingOverlay accessibleLabel="Loading"/>;
        }

        return (
            <div className="Login TopLevelContainer">
                <DocumentTitle title="FAIR Data Point | Log in"/>

                <div className="Skip"/>

                <div className="LoginContainer">

                    <div className="LoginLogo">
                        <CastorLogo className="Logo"/>
                    </div>

                    <LoginForm path={path} server={selectedServer} serverLocked={serverLocked} servers={servers}
                               catalog={catalog} view={view}/>

                </div>
            </div>
        );
    }
}

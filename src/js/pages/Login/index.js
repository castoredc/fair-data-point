import React, {Component} from "react";

import DocumentTitle from "../../components/DocumentTitle";
import './Login.scss';
import Button from "react-bootstrap/Button";
import queryString from 'query-string';
import axios from "axios";
import {toast} from "react-toastify";
import ToastContent from "../../components/ToastContent";
import LoadingScreen from "../../components/LoadingScreen";
import {localizedText} from "../../util";
import Logo from "../../components/Logo";
import ListItem from "../../components/ListItem";

export default class Login extends Component {
    constructor(props) {
        super(props);

        this.state = {
            servers: [],
            catalog: null,
            isLoading: true,
            selectedServer: null
        };
    }

    componentDidMount() {
        if(typeof this.props.match.params.catalogSlug !== 'undefined') {
            this.getCatalog(this.props.match.params.catalogSlug);
        }

        this.getServers();
    }

    getServers = () => {
        axios.get('/api/servers')
            .then((response) => {
                const params = queryString.parse(this.props.location.search);
                const defaultServer = response.data.filter((server) => server.default)[0].id;
                const serverIds = response.data.map((server) => server.id);
                const urlParamServer = (typeof params.server !== 'undefined') ? parseInt(params.server) : null;
                const selectedServer = (urlParamServer !== null && serverIds.includes(urlParamServer)) ? urlParamServer : defaultServer;

                this.setState({
                    servers:   response.data,
                    isLoading: false,
                    selectedServer: selectedServer
                });
            })
            .catch((error) => {
                this.setState({
                    isLoading: false,
                });
                toast.error(<ToastContent type="error" message="An error occurred"/>);
            });
    };

    getCatalog = (catalog) => {
        axios.get('/api/brand/' + catalog)
            .then((response) => {
                this.setState({
                    catalog:   response.data,
                    isLoading: false
                });
            })
            .catch((error) => {
                this.setState({
                    isLoading: false,
                });
                toast.error(<ToastContent type="error" message="An error occurred"/>);
            });
    };

    handleServerSelect = (serverId) => {
        this.setState({
            selectedServer: serverId,
            submitDisabled: false
        })
    };

    render() {
        const params = queryString.parse(this.props.location.search);
        const loginUrl = '/connect/castor/' + this.state.selectedServer + (typeof params.path !== 'undefined' ? '?target_path=' + params.path : '');

        if(this.state.isLoading)
        {
            return <LoadingScreen showLoading={true}/>;
        }

        const brand = this.state.catalog !== null ? localizedText(this.state.catalog.name, 'en') : 'FAIR Data Point';

        return (
            <div className="Login TopLevelContainer">
                <DocumentTitle title={brand + ' | Log in'} />

                <div className="Skip"/>

                <div className="LoginContainer">

                    <div className="LoginLogo">
                        <Logo />
                    </div>

                    {this.state.catalog !== null ? <div className="LoginBrand">
                        <h1>{brand}</h1>

                        <div className="LoginText">
                            <p>To enter your study in the {brand} you must be a registered Castor user.
                                Please log in with your Castor EDC account and allow the application to access your information.</p>
                            {this.state.catalog.accessingData === false && <p>
                                The application only accesses high-level information from your study and will not download nor upload any data to your study.
                            </p>}
                        </div>
                    </div> : <div>
                        <h1>{brand}</h1>

                        <div className="LoginText">
                            You need to be a registered Castor user in order to access this page.
                            Please log in with your Castor EDC account and allow the application to access your information.
                        </div>
                    </div>}

                    <div className="Servers">
                        <div className="ServerText">
                            My study is located on a Castor server in
                        </div>
                        <div className="ServersList">
                            {this.state.servers.map((server) => {
                                return <ListItem key={server.id}
                                                 title={server.name}
                                                 selectable={true}
                                                 active={this.state.selectedServer === server.id}
                                                 onClick={() => {this.handleServerSelect(server.id)}}
                                                 leftIcon={'flag' + server.flag.toUpperCase()}
                                                 className="ServerListItem"
                                                 fill={false}
                                />
                            })}
                        </div>
                    </div>


                    <div className="LoginButton">
                        <Button href={loginUrl} disabled={this.state.selectedServer === null}>Proceed</Button>
                    </div>
                </div>
            </div>
        );
    }
}

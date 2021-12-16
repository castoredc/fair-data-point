import React, {Component} from "react";
import {localizedText} from "../../util";
import ListItem from "../../components/ListItem";
import {Button, CastorNest} from "@castoredc/matter";
import {LoginViews} from "../../components/MetadataItem/EnumMappings";
import './LoginForm.scss';

export default class LoginForm extends Component {
    constructor(props) {
        super(props);

        this.state = {
            selectedServer: null,
        };
    }

    componentDidMount() {
        this.getSelectedServer();
    }

    getSelectedServer = () => {
        const {server, servers} = this.props;

        const defaultServer = servers.filter((server) => server.default)[0].id;
        const serverIds = servers.map((server) => server.id);

        this.setState({
            selectedServer: (server && serverIds.includes(server)) ? server : defaultServer,
        });
    };

    handleServerSelect = (serverId) => {
        this.setState({
            selectedServer: serverId,
        })
    };

    render() {
        const {catalog, path, servers, serverLocked = false, modal = false , brand = 'FAIR Data Point', view} = this.props;
        const {selectedServer} = this.state;

        const loginUrl = '/connect/castor/' + selectedServer + (path ? '?target_path=' + path : '');

        const viewName = LoginViews[view] || LoginViews['generic'];

        return (
            <div className="LoginForm">
                {catalog ? <div className="LoginBrand">
                    {!modal && <h1>{localizedText(catalog.name, 'en')}</h1>}

                    <div className="LoginText">
                        <p>To enter your study in the {localizedText(catalog.name, 'en')} you must be a registered Castor EDC user.</p>
                        <p>Please log in with your Castor EDC account and allow the application to access your
                            information.</p>
                        {catalog.accessingData === false && <p>
                            The application only accesses high-level information from your study and will not download
                            nor upload any data to your study.
                        </p>}
                    </div>
                </div> : <div>
                    {!modal && <h1>{brand}</h1>}

                    <div className="LoginText">
                        {(!modal) && <p>You need to be a registered Castor EDC user in order to access this {viewName}.</p>}
                        {(modal && view !== 'generic' && view !== null) && <p>You need to be a registered Castor EDC user in order to access this {viewName}.</p>}

                        <p>Please log in with your Castor EDC account and allow the application to access your
                            information.</p>
                    </div>
                </div>}

                {!serverLocked && <div className="Servers">
                    <div className="ServerText">
                        My study is located on a Castor server in
                    </div>
                    <div className="ServersList">
                        {servers.map((server) => {
                            return <ListItem key={server.id}
                                             title={server.name}
                                             selectable={true}
                                             active={selectedServer === server.id}
                                             onClick={() => {
                                                 this.handleServerSelect(server.id)
                                             }}
                                             customIcon={'flag' + server.flag.toUpperCase()}
                                             className="ServerListItem"
                                             fill={false}
                            />
                        })}
                    </div>
                </div>}


                <div className="LoginButton">
                    <Button href={loginUrl} disabled={selectedServer === null}>
                        <CastorNest className="LoginButtonLogo" />
                        Log in with Castor
                    </Button>
                </div>
            </div>
        );
    }
}

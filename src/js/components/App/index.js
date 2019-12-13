import React, {Component} from 'react';

import Routes from "../../Routes";
import Logo from "../Logo";

import '../../scss/index.scss'
import './App.scss'
import {Link} from "react-router-dom";

class App extends Component {
    constructor(props) {
        super(props);
        this.state = {
            isLoading: true
        };
    }

    componentDidMount() {
        this.setState({isLoading: false});
    }

    render() {
        return (
            <div>
                <div className={this.state.isLoading ? 'App Loading' : 'App Loaded'}>
                    {/*<Alert variant="warning" className="UnderDevelopment">*/}
                    {/*    <strong>Notice</strong> Please be aware that this FAIR Data Point (FDP) is still under development and that the (meta)data in this FDP may be dummy data.*/}
                    {/*</Alert>*/}
                    <div className="LogoOverlay">
                        <Link to="/fdp">
                            <Logo />
                        </Link>
                    </div>
                    <Routes />
                </div>
            </div>
        );
    }
}

export default App

import React, {Component} from 'react';

import Routes from "../../Routes";

import '../../scss/index.scss'
import './App.scss'


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
                    <Routes />
                </div>
            </div>
        );
    }
}

export default App

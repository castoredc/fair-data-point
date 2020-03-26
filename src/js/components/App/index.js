import React, {Component} from 'react';

import Routes from "../../Routes";
import Logo from "../Logo";

import '../../scss/index.scss'
import './App.scss'
import {Link} from "react-router-dom";
import {toast, ToastContainer} from "react-toastify";
import axios from "axios";
import ToastContent from "../ToastContent";
import LoadingScreen from "../LoadingScreen";

class App extends Component {
    constructor(props) {
        super(props);
        this.state = {
            isLoading: true,
            user: null
        };
    }

    getUser = () => {
        axios.get('/api/user')
            .then((response) => {
                let user = null;

                if(Object.keys(response.data).length !== 0)
                {
                    user = response.data;
                }

                this.setState({
                    user: user,
                    isLoading: false
                });
            })
            .catch((error) => {
                this.setState({
                    isLoading: false,
                });

                toast.error(<ToastContent type="error" message="An error occurred" />);
            });
    };

    componentDidMount() {
        this.getUser();
    }

    render() {
        return (
            <div>
                <div className={this.state.isLoading ? 'App Loading' : 'App Loaded'}>
                    <ToastContainer
                        position="top-center"
                        autoClose={5000}
                        hideProgressBar={false}
                        newestOnTop={false}
                        closeOnClick
                        rtl={false}
                        pauseOnVisibilityChange
                        draggable={false}
                        pauseOnHover
                    />
                    {this.state.isLoading ? <LoadingScreen showLoading={true}/> : <Routes user={this.state.user} />}
                </div>
            </div>
        );
    }
}

export default App

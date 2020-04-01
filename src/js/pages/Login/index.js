import React, { Component } from "react";

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

export default class Login extends Component {
    constructor(props) {
        super(props);

        this.state = {
            catalog: null,
            isLoading: true
        };
    }

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

    componentDidMount() {
        if(typeof this.props.match.params.catalogSlug !== 'undefined') {
            this.getCatalog(this.props.match.params.catalogSlug);
        }
        else
        {
            this.setState({
                isLoading: false,
            });
        }
    }

    render() {
        const params = queryString.parse(this.props.location.search);
        const loginUrl = '/connect/castor' + (typeof params.path !== 'undefined' ? '?target_path=' + params.path : '');

        if(this.state.isLoading)
        {
            return <LoadingScreen showLoading={true}/>;
        }

        return (
            <div className="Login TopLevelContainer">
                <DocumentTitle title="Log in" />

                <div className="Skip"/>

                <div className="LoginContainer">

                    <div className="LoginLogo">
                        <Logo />
                    </div>

                    {this.state.catalog !== null ? <div className="LoginBrand">
                        <h1>{localizedText(this.state.catalog.name, 'en')}</h1>

                        <div className="LoginText">
                            <p>To enter your study in the {localizedText(this.state.catalog.name, 'en')} you must be a registered Castor user.
                                Please log in with your Castor EDC account and allow the application to access your information.</p>
                            {this.state.catalog.accessingData === false && <p>
                                The application only accesses high-level information from your study and will not download nor upload any data to your study.
                            </p>}
                        </div>
                    </div> : <div>
                        <h1>FAIR Data Point</h1>

                        <div className="LoginText">
                            You need to be a registered Castor user in order to access this page.
                            Please log in with your Castor EDC account and allow the application to access your information.
                        </div>
                    </div>}

                    <div className="LoginButton">
                        <Button href={loginUrl}>Proceed</Button>
                    </div>
                </div>
            </div>
        );
    }
}

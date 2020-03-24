import React, { Component } from "react";

import DocumentTitle from "../../components/DocumentTitle";
import './Login.scss';
import Button from "react-bootstrap/Button";
import queryString from 'query-string';

export default class Login extends Component {
    render() {
        const params = queryString.parse(this.props.location.search);
        const loginUrl = '/connect/castor' + (typeof params.path !== 'undefined' ? '?target_path=' + params.path : '');

        return (
            <div className="Login TopLevelContainer">
                <DocumentTitle title="Log in" />

                <div className="Skip"/>

                <div className="LoginContainer">
                    <h1>You need to be logged in in order to access this page</h1>

                    <p>
                        Please log in with your Castor EDC account and allow the application to access your information.
                    </p>

                    <Button href={loginUrl}>Log in</Button>
                </div>
            </div>
        );
    }
}

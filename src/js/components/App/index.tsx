import React, { Component } from 'react';

import Routes from '../../Routes';

import '../../scss/index.scss';
import './App.scss';
import { toast, ToastContainer } from 'react-toastify';
import ToastContent from '../ToastContent';
import { RouteComponentProps, withRouter } from 'react-router-dom';
import queryString from 'query-string';
import { classNames } from '../../util';
import { LoadingOverlay } from '@castoredc/matter';
import { UserType } from '../../types/UserType';
import TagManager from 'react-gtm-module';
import { apiClient } from 'src/js/network';

interface AppProps extends RouteComponentProps<any> {}

interface AppState {
    isLoading: boolean;
    user: UserType | null;
}

class App extends Component<AppProps, AppState> {
    constructor(props) {
        super(props);
        this.state = {
            isLoading: true,
            user: null,
        };
    }

    componentDidMount() {
        this.getUser();
    }

    componentDidUpdate(prevProps) {
        if (this.props.location !== prevProps.location) {
            this.onRouteChange();
        }
    }

    onRouteChange = () => {
        TagManager.dataLayer({
            dataLayer: {
                event: 'FdpPageView',
                url: this.props.location.pathname,
            },
        });
    };

    getUser = () => {
        apiClient
            .get('/api/user')
            .then(response => {
                let user = null;

                if (Object.keys(response.data).length !== 0) {
                    user = response.data;
                }

                this.setState({
                    user: user,
                    isLoading: false,
                });
            })
            .catch(error => {
                this.setState({
                    isLoading: false,
                });

                toast.error(<ToastContent type="error" message="An error occurred" />);
            });
    };

    render() {
        const { isLoading, user } = this.state;

        const params = queryString.parse(window.location.search);
        const embedded = typeof params.embed !== 'undefined';

        return (
            <div className={classNames('App', embedded && 'Embedded')}>
                <ToastContainer
                    position="top-center"
                    autoClose={5000}
                    hideProgressBar={false}
                    newestOnTop={false}
                    closeOnClick
                    rtl={false}
                    draggable={false}
                    pauseOnHover
                />
                {isLoading ? <LoadingOverlay accessibleLabel="Loading" /> : <Routes user={user} embedded={embedded} />}
            </div>
        );
    }
}

export default withRouter(App);

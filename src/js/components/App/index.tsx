import React, {Component} from 'react';

import Routes from '../../Routes';

import '../../scss/index.scss';
import './App.scss';
import {toast, ToastContainer} from 'react-toastify';
import {ToastMessage} from '@castoredc/matter';
import queryString from 'query-string';
import {classNames} from '../../util';
import {LoadingOverlay} from '@castoredc/matter';
import {UserType} from 'types/UserType';
import {apiClient} from 'src/js/network';

interface AppState {
    isLoading: boolean;
    user: UserType | null;
}

class App extends Component<{}, AppState> {
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

                toast.error(<ToastMessage type="error" title="An error occurred" />);
            });
    };

    render() {
        const { isLoading, user } = this.state;

        const params = queryString.parse(window.location.search);
        const embedded = typeof params.embed !== 'undefined';

        return (
            <div className={classNames('App', embedded && 'Embedded')}>
                <ToastContainer
                    position="top-right"
                    autoClose={10000}
                    hideProgressBar={true}
                    newestOnTop={false}
                    closeOnClick
                    rtl={false}
                    draggable={false}
                    pauseOnHover
                    icon={false}
                />
                {isLoading ? <LoadingOverlay accessibleLabel="Loading" /> : <Routes user={user} embedded={embedded} />}
            </div>
        );
    }
}

export default App;

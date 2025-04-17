import React, { Component } from 'react';

import Routes from '../../Routes';

import LoadingOverlay from 'components/LoadingOverlay';
import queryString from 'query-string';
import { classNames } from '../../util';
import { UserType } from 'types/UserType';
import { apiClient } from 'src/js/network';
import WithNotifications, { ComponentWithNotifications } from 'components/WithNotifications';
import { createTheme, ThemeProvider, styled } from '@mui/material/styles';

interface AppState {
    isLoading: boolean;
    user: UserType | null;
}

class App extends Component<ComponentWithNotifications, AppState> {
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
        const { notifications } = this.props;

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

                notifications.show('An error occurred', { variant: 'error' });
            });
    };

    render() {
        const { isLoading, user } = this.state;

        const params = queryString.parse(window.location.search);
        const embedded = typeof params.embed !== 'undefined';

        const theme = createTheme({

        });

        return (
            <ThemeProvider theme={theme}>
                <div className={classNames('App', embedded && 'Embedded')}>
                    {isLoading ? <LoadingOverlay accessibleLabel="Loading" /> : <Routes user={user} embedded={embedded} />}
                </div>
            </ThemeProvider>
        );
    }
}

export default WithNotifications(App);

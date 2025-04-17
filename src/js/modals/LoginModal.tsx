import React, { Component } from 'react';
import Modal from 'components/Modal';
import LoginForm from 'components/Form/LoginForm';
import { ServerType } from 'types/ServerType';
import { apiClient } from '../network';
import withNotifications, { ComponentWithNotifications } from 'components/WithNotifications';

interface LoginModalProps extends ComponentWithNotifications {
    show: boolean;
    handleClose: () => void;
    path: string;
    server?: string;
    view?: string;
};

type LoginModalState = {
    servers: ServerType[];
    isLoading: boolean;
};

class LoginModal extends Component<LoginModalProps, LoginModalState> {
    constructor(props) {
        super(props);

        this.state = {
            servers: [],
            isLoading: true,
        };
    }

    componentDidMount() {
        this.getServers();
    }

    getServers = () => {
        const { notifications } = this.props;

        apiClient
            .get('/api/castor/servers')
            .then(response => {
                this.setState({
                    servers: response.data,
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
        const { show, handleClose, path, server, view } = this.props;
        const { servers, isLoading } = this.state;

        return (
            <Modal open={show} onClose={handleClose} title="Log in" customWidth="50rem" isLoading={isLoading}>
                <LoginForm path={path} modal={true} selectedServerId={server} serverLocked={!!server} servers={servers}
                           view={view} />
            </Modal>
        );
    }
}

export default withNotifications(LoginModal);
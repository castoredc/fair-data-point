import React, { Component } from 'react';
import { toast } from 'react-toastify';
import {ToastMessage} from '@castoredc/matter';
import LoginForm from 'components/Form/LoginForm';
import { Modal } from '@castoredc/matter';
import { ServerType } from 'types/ServerType';
import { apiClient } from '../network';

type LoginModalProps = {
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

export default class LoginModal extends Component<LoginModalProps, LoginModalState> {
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
                toast.error(<ToastMessage type="error" title="An error occurred" />);
            });
    };

    render() {
        const { show, handleClose, path, server, view } = this.props;
        const { servers, isLoading } = this.state;

        return (
            <Modal open={show} onClose={handleClose} title="Log in" accessibleName="Log in" customWidth="50rem" isLoading={isLoading}>
                <LoginForm path={path} modal={true} selectedServerId={server} serverLocked={!!server} servers={servers} view={view} />
            </Modal>
        );
    }
}

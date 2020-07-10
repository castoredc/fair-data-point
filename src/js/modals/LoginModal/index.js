import React, {Component} from 'react'
import {ValidatorForm} from "react-form-validator-core";
import axios from "axios";
import {toast} from "react-toastify";
import ToastContent from "../../components/ToastContent";
import Dropdown from "../../components/Input/Dropdown";
import FormItem from "../../components/Form/FormItem";
import {Button} from "@castoredc/matter";
import Modal from "../Modal";
import LoginForm from "../../components/Form/LoginForm";
import InlineLoader from "../../components/LoadingScreen/InlineLoader";

export default class LoginModal extends Component {
    constructor(props) {
        super(props);

        this.state = {
            servers:        [],
            isLoading:      true,
        };
    }

    componentDidMount() {
        this.getServers();
    }

    getServers = () => {
        const {server } = this.props;

        axios.get('/api/castor/servers')
            .then((response) => {
                this.setState({
                    servers:        response.data,
                    isLoading:      false,
                });
            })
            .catch((error) => {
                this.setState({
                    isLoading: false,
                });
                toast.error(<ToastContent type="error" message="An error occurred"/>);
            });
    };

    render() {
        const {show, handleClose, path, server, view} = this.props;
        const {servers, isLoading} = this.state;

        return <Modal
            show={show}
            handleClose={handleClose}
            className="LoginModal"
            title="Log in"
            closeButton
        >
            {isLoading ? <InlineLoader /> : <LoginForm path={path} modal={true} server={server} serverLocked={!!server} servers={servers} view={view} />}
        </Modal>
    }
}
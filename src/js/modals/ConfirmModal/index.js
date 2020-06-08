import React, {Component} from 'react'
import Modal from "react-bootstrap/Modal";
import Container from "react-bootstrap/Container";
import {Button, Stack} from "@castoredc/matter";
import './ConfirmModal.scss';

export default class ConfirmModal extends Component {
    constructor(props) {
        super(props);

        this.state = {
            showCancelModal: props.show ? props.show : false
        };
    }

    componentDidUpdate(prevProps, prevState, snapshot) {
        const { show } = this.props;

        if (show !== prevProps.show) {
            this.setState({
                showCancelModal: show
            });
        }
    }

    handleClick = (e) => {
        e.preventDefault();

        this.setState({
            showCancelModal: true,
        });
    };

    handleCancel = () => {
        const { onCancel = () => {} }  = this.props;

        this.setState({
            showCancelModal: false,
        }, () => {
            onCancel();
        });
    };

    handleConfirm = () => {
        const { onConfirm } = this.props;

        onConfirm(() => {
            this.setState({
                showCancelModal: false,
            });
        });
    };

    render() {
        const { title, children, action, variant, includeButton = false } = this.props;
        const { showCancelModal } = this.state;

        const modal = <Modal
            show={showCancelModal}
            className="ConfirmModal"
            backdropClassName="ConfirmModalBackdrop"
            backdrop="static"
        >
            <Modal.Header>
                <Modal.Title>{title}</Modal.Title>
            </Modal.Header>
            <Modal.Body>
                <Container>
                    {children}
                </Container>
            </Modal.Body>
            <Modal.Footer>
                <Button buttonType={variant ? variant : 'primary'} onClick={this.handleConfirm}>
                    {action}
                </Button>
                <Button buttonType="contentOnly" onClick={this.handleCancel}>
                    Cancel
                </Button>
            </Modal.Footer>
        </Modal>;

        if(includeButton) {
            return <div className="Confirm">
                <Button buttonType={variant ? variant : 'primary'} onClick={this.handleClick}>
                    {action}
                </Button>
                {modal}
            </div>
        } else {
            return modal;
        }
    }
}
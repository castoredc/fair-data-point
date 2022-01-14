import React, {Component} from 'react'
import {Button} from "@castoredc/matter";
import './ConfirmModal.scss';
import Modal from "../Modal";

export default class ConfirmModal extends Component {
    constructor(props) {
        super(props);

        this.state = {
            showCancelModal: props.show ? props.show : false
        };
    }

    componentDidUpdate(prevProps, prevState, snapshot) {
        const {show} = this.props;

        if (show !== prevProps.show) {
            this.setState({
                showCancelModal: show
            });
        }
    }

    handleClick = () => {
        this.setState({
            showCancelModal: true,
        });
    };

    handleCancel = () => {
        const {
            onCancel = () => {
            }
        } = this.props;

        this.setState({
            showCancelModal: false,
        }, () => {
            onCancel();
        });
    };

    handleConfirm = () => {
        const {onConfirm} = this.props;

        onConfirm(() => {
            this.setState({
                showCancelModal: false,
            });
        });
    };

    render() {
        const {title, children, action, variant, includeButton = false} = this.props;
        const {showCancelModal} = this.state;

        const modal = <Modal
            show={showCancelModal}
            className="ConfirmModal"
            title={title}
            footer={(
                <div>
                    <Button buttonType={variant ? variant : 'primary'} onClick={this.handleConfirm}>
                        {action}
                    </Button>
                    <Button buttonType="contentOnly" onClick={this.handleCancel}>
                        Cancel
                    </Button>
                </div>
            )}
        >
            {children}
        </Modal>;

        if (includeButton) {
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
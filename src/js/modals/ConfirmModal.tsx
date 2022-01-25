import React, {Component} from 'react'
import {Button, ButtonVariants, Modal} from "@castoredc/matter";

type ConfirmModalProps = {
    show?: boolean,
    title: string,
    action: string,
    variant: ButtonVariants,
    onCancel?: () => void,
    onConfirm: (callback ?: () => void) => void,
}

type ShowProps =
    | { includeButton: true; show: never}
    | { includeButton?: false; show: boolean}

type ConfirmModalState = {
    showCancelModal: boolean,
}

export default class ConfirmModal extends Component<ConfirmModalProps & ShowProps, ConfirmModalState> {
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
        const {onCancel} = this.props;

        this.setState({
            showCancelModal: false,
        }, () => {
            if(onCancel) {
                onCancel();
            }
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
            open={showCancelModal}
            title={title}
            accessibleName={title}
            onClose={this.handleCancel}
            primaryAction={{
                label: action,
                onClick: this.handleConfirm
            }}
            secondaryActions={[
                {
                    label: 'Cancel',
                    onClick: this.handleCancel,
                    buttonType: 'contentOnly'
                }
            ]}
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
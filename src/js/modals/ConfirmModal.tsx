import React, { Component } from 'react';
import Button from '@mui/material/Button';
import Modal from 'components/Modal';
import Stack from '@mui/material/Stack';

type ConfirmModalProps = {
    show?: boolean;
    title: string;
    action: string;
    variant: 'text' | 'contained' | 'outlined';
    color?: 'inherit' | 'primary' | 'secondary' | 'success' | 'error' | 'info' | 'warning';
    onCancel?: () => void;
    onConfirm: (callback?: () => void) => void;
    children?: React.ReactNode
};

type ShowProps = { includeButton: true } | { includeButton?: false; show: boolean };

type ConfirmModalState = {
    showCancelModal: boolean;
};

class ConfirmModal extends Component<ConfirmModalProps & ShowProps, ConfirmModalState> {
    constructor(props) {
        super(props);

        this.state = {
            showCancelModal: props.show ? props.show : false,
        };
    }

    componentDidUpdate(prevProps, prevState, snapshot) {
        const { show } = this.props;

        if (show !== prevProps.show) {
            this.setState({
                showCancelModal: show ?? false,
            });
        }
    }

    handleClick = () => {
        this.setState({
            showCancelModal: true,
        });
    };

    handleCancel = () => {
        const { onCancel } = this.props;

        this.setState(
            {
                showCancelModal: false,
            },
            () => {
                if (onCancel) {
                    onCancel();
                }
            },
        );
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
        const { title, children, action, variant, color, includeButton = false } = this.props;
        const { showCancelModal } = this.state;

        const modal = (
            <Modal
                open={showCancelModal}
                title={title}
                onClose={this.handleCancel}
            >
                {children}

                <div>
                    <Stack direction="row" sx={{ justifyContent: 'space-between' }}>
                        <Button variant="outlined" onClick={this.handleConfirm}>
                            {action}
                        </Button>
                        <Button onClick={this.handleCancel}>
                            Cancel
                        </Button>
                    </Stack>
                </div>
            </Modal>
        );

        if (includeButton) {
            return (
                <div className="Confirm">
                    <Button variant={variant ? variant : 'contained'} color={color} onClick={this.handleClick}>
                        {action}
                    </Button>
                    {modal}
                </div>
            );
        } else {
            return modal;
        }
    }
}

export default ConfirmModal;
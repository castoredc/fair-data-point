import React, { Component } from 'react';
import { Modal } from '@castoredc/matter';
import PersonForm from '../components/Form/Agent/PersonForm';
import { toast } from 'react-toastify';
import {ToastMessage} from '@castoredc/matter';
import { apiClient } from '../network';

type ContactModalProps = {
    email?: string | undefined;
    open: boolean;
    onClose: () => void;
    studyId: string;
};

type ContactModalState = {};

export default class ContactModal extends Component<ContactModalProps, ContactModalState> {
    constructor(props) {
        super(props);
    }

    handleSubmit = (values, { setSubmitting }) => {
        const { studyId, onClose } = this.props;

        window.onbeforeunload = null;

        const name = [values.firstName, values.middleName, values.lastName].filter(Boolean).join(' ');
        this.setState({
            isLoading: true,
        });

        apiClient
            .post('/api/study/' + studyId + '/team/add', values)
            .then(response => {
                this.setState({
                    isLoading: false,
                });

                toast.success(<ToastMessage type="success" title={`${name} was successfully added as study contact`} />, {
                    position: 'top-right',
                });

                onClose();
            })
            .catch(error => {
                if (error.response && error.response.status === 400) {
                    this.setState({
                        validation: error.response.data.fields,
                    });
                } else {
                    toast.error(<ToastMessage type="error" title="An error occurred" />);
                }
                this.setState(
                    {
                        isLoading: false,
                    },
                    () => {
                        setSubmitting(false);
                    }
                );
            });
    };

    render() {
        const { open, email, onClose, studyId } = this.props;

        const edit = !!email;
        const title = edit ? `Edit contact` : 'Add contact';

        return (
            <Modal open={open} title={title} accessibleName={title} onClose={onClose}>
                <PersonForm email={email} studyId={studyId} handleSubmit={this.handleSubmit} />
            </Modal>
        );
    }
}

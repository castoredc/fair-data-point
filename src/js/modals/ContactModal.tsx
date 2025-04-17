import React, { Component } from 'react';
import Modal from 'components/Modal';
import PersonForm from '../components/Form/Agent/PersonForm';
import { apiClient } from '../network';
import withNotifications, { ComponentWithNotifications } from 'components/WithNotifications';

interface ContactModalProps extends ComponentWithNotifications {
    email?: string | undefined;
    open: boolean;
    onClose: () => void;
    studyId: string;
};

type ContactModalState = {};

class ContactModal extends Component<ContactModalProps, ContactModalState> {
    constructor(props) {
        super(props);
    }

    handleSubmit = (values, { setSubmitting }) => {
        const { studyId, onClose, notifications } = this.props;

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

                notifications.show(`${name} was successfully added as study contact`, {
                    variant: 'success',

                });

                onClose();
            })
            .catch(error => {
                if (error.response && error.response.status === 400) {
                    this.setState({
                        validation: error.response.data.fields,
                    });
                } else {
                    notifications.show('An error occurred', { variant: 'error' });
                }
                this.setState(
                    {
                        isLoading: false,
                    },
                    () => {
                        setSubmitting(false);
                    },
                );
            });
    };

    render() {
        const { open, email, onClose, studyId } = this.props;

        const edit = !!email;
        const title = edit ? `Edit contact` : 'Add contact';

        return (
            <Modal open={open} title={title} onClose={onClose}>
                <PersonForm email={email} studyId={studyId} handleSubmit={this.handleSubmit} />
            </Modal>
        );
    }
}


export default withNotifications(ContactModal);
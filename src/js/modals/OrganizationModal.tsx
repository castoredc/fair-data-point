import React, { Component } from 'react';
import Modal from 'components/Modal';
import OrganizationForm from 'components/Form/Agent/OrganizationForm';
import { apiClient } from '../network';
import withNotifications, { ComponentWithNotifications } from 'components/WithNotifications';

interface OrganizationModalProps extends ComponentWithNotifications {
    id?: string;
    open: boolean;
    onClose: () => void;
    studyId: string;
    countries: any;
};

type OrganizationModalState = {};

class OrganizationModal extends Component<OrganizationModalProps, OrganizationModalState> {
    constructor(props) {
        super(props);
    }

    handleSubmit = (values, { setSubmitting }) => {
        const { studyId, onClose, notifications } = this.props;

        window.onbeforeunload = null;

        this.setState({
            isLoading: true,
        });

        apiClient
            .post('/api/study/' + studyId + '/centers/add', {
                source: values.organization.source,
                country: values.country,
                ...(values.organization.source !== 'database'
                    ? {
                        name: values.organization.name,
                        city: values.organization.city,
                    }
                    : {
                        id: values.organization.id,
                    }),
            })
            .then(response => {
                this.setState({
                    isLoading: false,
                });

                onClose();

                notifications.show(`The ${values.organization.name} center was successfully added.`, {
                    variant: 'success',

                });
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
        const { open, id, countries, onClose } = this.props;

        const edit = !!id;
        const title = edit ? `Edit center` : 'Add organization';

        return (
            <Modal open={open} title={title} onClose={onClose}>
                <OrganizationForm countries={countries} handleSubmit={this.handleSubmit} />
            </Modal>
        );
    }
}

export default withNotifications(OrganizationModal);
import React, { Component } from 'react';
import { Modal } from '@castoredc/matter';
import OrganizationForm from 'components/Form/Agent/OrganizationForm';
import { toast } from 'react-toastify';
import ToastContent from 'components/ToastContent';
import { apiClient } from '../network';

type OrganizationModalProps = {
    id?: string;
    open: boolean;
    onClose: () => void;
    studyId: string;
    countries: any;
};

type OrganizationModalState = {};

export default class OrganizationModal extends Component<OrganizationModalProps, OrganizationModalState> {
    constructor(props) {
        super(props);
    }

    handleSubmit = (values, { setSubmitting }) => {
        const { studyId, onClose } = this.props;

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

                toast.success(<ToastContent type="success" message={`The ${values.organization.name} center was successfully added.`} />, {
                    position: 'top-right',
                });
            })
            .catch(error => {
                if (error.response && error.response.status === 400) {
                    this.setState({
                        validation: error.response.data.fields,
                    });
                } else {
                    toast.error(<ToastContent type="error" message="An error occurred" />);
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
        const { open, id, countries, onClose } = this.props;

        const edit = !!id;
        const title = edit ? `Edit center` : 'Add center';

        return (
            <Modal open={open} title={title} accessibleName={title} onClose={onClose}>
                <OrganizationForm countries={countries} handleSubmit={this.handleSubmit} />
            </Modal>
        );
    }
}

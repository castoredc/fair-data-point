import React, {Component} from 'react'
import {Modal} from "@castoredc/matter";
import OrganizationForm from "components/Form/Agent/OrganizationForm";
import axios from "axios";
import {toast} from "react-toastify";
import ToastContent from "components/ToastContent";

type OrganizationModalProps = {
    id?: string,
    open: boolean,
    onClose: () => void,
    studyId: string,
    countries: any
}

type OrganizationModalState = {
}


export default class OrganizationModal extends Component<OrganizationModalProps, OrganizationModalState> {
    constructor(props) {
        super(props);
    }

    handleSubmit = (values, { setSubmitting }) => {
        const {studyId} = this.props;

        window.onbeforeunload = null;

        this.setState({
            isLoading:      true,
        });

        axios.post('/api/study/' + studyId + '/centers/add', {
            source: values.source,
            country: values.country.value,
            ...values.source !== 'database' ? {
                name: values.name,
                city: values.city,
            } : {
                id: values.id,
            }
        })
            .then((response) => {
                this.setState({
                    isLoading:      false,
                });

                toast.success(<ToastContent type="success" message={`The ${values.name} center was successfully added.`}/>, {
                    position: "top-right",
                });
            })
            .catch((error) => {
                if (error.response && error.response.status === 400) {
                    this.setState({
                        validation: error.response.data.fields,
                    });
                } else {
                    toast.error(<ToastContent type="error" message="An error occurred"/>, {
                        position: "top-center",
                    });
                }
                this.setState({
                    isLoading:      false,
                }, () => {
                    setSubmitting(false);
                });
            });
    }

    render() {
        const {open, id, countries, onClose} = this.props;

        const edit = !! id;
        const title = edit ? `Edit center` : 'Add center';
        
        return <Modal
            open={open}
            title={title}
            accessibleName={title}
            onClose={onClose}
        >
            <OrganizationForm countries={countries} handleSubmit={this.handleSubmit} />
        </Modal>
    }
}
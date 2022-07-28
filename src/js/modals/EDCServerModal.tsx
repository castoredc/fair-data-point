import React, {Component} from 'react'
import {Choice, Modal} from "@castoredc/matter";
import PersonForm from "components/Form/Agent/PersonForm";
import OrganizationForm from "components/Form/Agent/OrganizationForm";
import EDCServerForm from "components/Form/Admin/EDCServerForm";

type EDCServerModalProps = {
    open: boolean,
    onClose: () => void,
    handleSave: (edcServer) => void,
}

type EDCServerModalState = {
}

export default class EDCServerModal extends Component<EDCServerModalProps, EDCServerModalState> {
    constructor(props) {
        super(props);

        this.state = {
        };
    }

    handleSubmit = (values, {setSubmitting}) => {
        const {handleSave} = this.props;

        handleSave(values);

        setSubmitting(false);
    }

    render() {
        const {open, onClose} = this.props;

        const title = 'Add new EDC server';

        return <Modal
            open={open}
            title={title}
            accessibleName={title}
            onClose={onClose}
        >
            <EDCServerForm handleSubmit={this.handleSubmit} />
        </Modal>
    }
}

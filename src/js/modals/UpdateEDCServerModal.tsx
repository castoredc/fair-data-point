import React, {Component} from 'react'
import {Modal} from "@castoredc/matter";
import EDCServerForm from "components/Form/Admin/EDCServerForm";
import {ServerType} from "types/ServerType";

type EDCServerModalProps = {
    open: boolean,
    onClose: () => void,
    data: ServerType|null,
    handleSave: (edcServer) => void,
}

type EDCServerModalState = {
    initialValues: any;
}

export default class UpdateEDCServerModal extends Component<EDCServerModalProps, EDCServerModalState> {
    constructor(props) {
        super(props);

        this.state = {
            initialValues: props.data ?? null,
        };
    }

    handleSubmit = (newServer) => {
        const {handleSave} = this.props;

        handleSave(newServer);
    }

    render() {
        const {open, onClose} = this.props;
        const {initialValues} = this.state;

        const title = initialValues && initialValues.length ? 'Update EDC server' : 'Add new EDC server';

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

import React, {Component} from 'react'
import {Modal} from "@castoredc/matter";
import EDCServerForm from "components/Form/Admin/EDCServerForm";
import {ServerType} from "types/ServerType";

type AddEDCServerModalProps = {
    open: boolean,
    onClose: () => void,
    handleSave: (edcServer: ServerType) => void,
}

type AddEDCServerModalState = {}

export default class AddEDCServerModal extends Component<AddEDCServerModalProps, AddEDCServerModalState> {
    constructor(props) {
        super(props);

        this.state = {};
    }

    handleSubmit = (newServer) => {
        const {handleSave} = this.props;

        handleSave(newServer);
    }

    render() {
        const {open, onClose} = this.props;

        const title = 'Add a new EDC server';

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

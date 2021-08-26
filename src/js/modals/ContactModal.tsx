import React, {Component} from 'react'
import {Button, Modal, Stack} from "@castoredc/matter";
import PersonForm from "../components/Form/Agent/PersonForm";

type ContactModalProps = {
    email?: string | undefined,
    open: boolean,
    onClose: () => void,
    studyId: string
}

type ContactModalState = {
}


export default class ContactModal extends Component<ContactModalProps, ContactModalState> {
    constructor(props) {
        super(props);
    }

    render() {
        const {open, email, onClose, studyId} = this.props;

        const edit = !! email;
        const title = edit ? `Edit contact` : 'Add contact';
        
        return <Modal
            open={open}
            title={title}
            accessibleName={title}
            onClose={onClose}
        >
            <PersonForm email={email} studyId={studyId} onSubmit={onClose} />
        </Modal>
    }
}
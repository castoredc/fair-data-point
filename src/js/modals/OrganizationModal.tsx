import React, {Component} from 'react'
import {Button, Modal, Stack} from "@castoredc/matter";
import PersonForm from "../components/Form/Agent/PersonForm";
import OrganizationForm from "components/Form/Agent/OrganizationForm";

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

    render() {
        const {open, id, countries, onClose, studyId} = this.props;

        const edit = !! id;
        const title = edit ? `Edit center` : 'Add center';
        
        return <Modal
            open={open}
            title={title}
            accessibleName={title}
            onClose={onClose}
        >
            <OrganizationForm id={id} countries={countries} studyId={studyId} onSubmit={onClose} />
        </Modal>
    }
}
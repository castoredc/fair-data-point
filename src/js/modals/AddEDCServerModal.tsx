import React from 'react'
import {Modal} from "@castoredc/matter";
import EDCServerForm from "components/Form/Admin/EDCServerForm";

const AddEDCServerModal = ({open, onClose, handleSave}) => {
    const handleSubmit = (newServer) => {
        handleSave(newServer);
    }
    const title = 'Add a new EDC server';

    return (
        <Modal
            open={open}
            title={title}
            accessibleName={title}
            onClose={onClose}
        >
            <EDCServerForm handleSubmit={handleSubmit}/>
        </Modal>
    )
}

export {AddEDCServerModal};

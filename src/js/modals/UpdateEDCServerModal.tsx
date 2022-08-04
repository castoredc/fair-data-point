import React from 'react'
import {Modal} from "@castoredc/matter";
import EDCServerForm from "components/Form/Admin/EDCServerForm";

const UpdateEDCServerModal = ({open, onClose, data, handleSave}) => {
    const title = 'Update EDC server';

    return (
        <Modal
            open={open}
            title={title}
            accessibleName={title}
            onClose={onClose}
        >
            <EDCServerForm handleSubmit={handleSave} edcServer={data}/>
        </Modal>
    )
}

export { UpdateEDCServerModal };

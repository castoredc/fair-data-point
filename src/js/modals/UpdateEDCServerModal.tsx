import React from 'react';
import Modal from 'components/Modal';
import { EDCServerForm } from 'components/Form/Admin/EDCServerForm';

const UpdateEDCServerModal = ({ open, onClose, data, handleSave }) => {
    const title = 'Update EDC server';

    return (
        <Modal open={open} title={title} onClose={onClose}>
            <EDCServerForm handleSubmit={handleSave} edcServer={data} />
        </Modal>
    );
};

export { UpdateEDCServerModal };

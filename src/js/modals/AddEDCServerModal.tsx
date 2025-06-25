import React from 'react';
import Modal from 'components/Modal';
import { EDCServerForm } from 'components/Form/Admin/EDCServerForm';

const AddEDCServerModal = ({ open, onClose, handleSave }) => {
    const title = 'Add a new EDC server';

    return (
        <Modal open={open} title={title} onClose={onClose}>
            <EDCServerForm handleSubmit={handleSave} />
        </Modal>
    );
};

export { AddEDCServerModal };

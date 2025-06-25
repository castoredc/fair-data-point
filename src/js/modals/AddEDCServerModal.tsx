import React from 'react';
import { Modal } from '@castoredc/matter';
import { EDCServerForm } from 'components/Form/Admin/EDCServerForm';

const AddEDCServerModal = ({ open, onClose, handleSave }) => {
    const title = 'Add a new EDC server';

    return (
        <Modal open={open} title={title} accessibleName={title} onClose={onClose}>
            <EDCServerForm handleSubmit={handleSave} />
        </Modal>
    );
};

export { AddEDCServerModal };

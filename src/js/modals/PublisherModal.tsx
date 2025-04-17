import React, { FC, useState } from 'react';
import PersonForm from 'components/Form/Agent/PersonForm';
import OrganizationForm from 'components/Form/Agent/OrganizationForm';
import { CountryType } from 'types/CountryType';
import Modal from 'components/Modal';
import RadioGroup from 'components/RadioGroup';
import { FormLabel } from '@mui/material';

type PublisherModalProps = {
    open: boolean;
    onClose: () => void;
    handleSave: (publisher) => void;
    countries: CountryType[];
    label: string,
};

const PublisherModal: FC<PublisherModalProps> = ({ open, label, onClose, handleSave, countries }) => {
    const [type, setType] = useState('person');

    const handleSubmit = (values, { setSubmitting }) => {
        if (type === 'organization') {
            handleSave({
                type: type,
                organization: {
                    ...values.organization,
                    country: values.country,
                },
            });
        } else {
            handleSave({
                type: type,
                [type]: values,
            });
        }

        setSubmitting(false);
    };

    const form = () => {
        switch (type) {
            case 'person':
                return <PersonForm handleSubmit={handleSubmit} />;
            case 'organization':
                return <OrganizationForm countries={countries} handleSubmit={handleSubmit} />;
        }
    };

    const title = `Add ${label}`;

    return (
        <Modal open={open} title={title} onClose={onClose}>
            <FormLabel>Type</FormLabel>

            <RadioGroup
                options={[
                    {
                        label: 'Person',
                        value: 'person',
                    },
                    {
                        label: 'Organization',
                        value: 'organization',
                    },
                ]}
                name="type"
                collapse={true}
                value={type}
                onChange={e => setType('value' in e.target ? (e.target.value as string) : '')}
            />

            {type === 'person' && <PersonForm handleSubmit={handleSubmit} />}

            {type === 'organization' && <OrganizationForm countries={countries} handleSubmit={handleSubmit} />}
        </Modal>
    );
};

export default PublisherModal;

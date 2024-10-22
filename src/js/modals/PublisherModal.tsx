import React, { FC, useState } from 'react';
import { Choice, Modal } from '@castoredc/matter';
import PersonForm from 'components/Form/Agent/PersonForm';
import OrganizationForm from 'components/Form/Agent/OrganizationForm';
import { CountryType } from 'types/CountryType';

type PublisherModalProps = {
    open: boolean;
    onClose: () => void;
    handleSave: (publisher) => void;
    countries: CountryType[];
};

const PublisherModal: FC<PublisherModalProps> = ({ open, onClose, handleSave, countries }) => {
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

    const title = 'Add publisher';

    return (
        <Modal open={open} title={title} accessibleName={title} onClose={onClose}>
            <Choice
                labelText="Type"
                options={[
                    {
                        labelText: 'Person',
                        value: 'person',
                        checked: type === 'person',
                    },
                    {
                        labelText: 'Organization',
                        value: 'organization',
                        checked: type === 'organization',
                    },
                ]}
                name="type"
                collapse={true}
                onChange={(e) => setType('value' in e.target ? e.target.value as string : '')}
            />

            {type === 'person' && <PersonForm handleSubmit={handleSubmit} />}

            {type === 'organization' && <OrganizationForm countries={countries} handleSubmit={handleSubmit} />}
        </Modal>
    );
}

export default PublisherModal;
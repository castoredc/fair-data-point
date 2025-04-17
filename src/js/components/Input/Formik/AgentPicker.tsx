import React, { FC, useState } from 'react';

import Button from '@mui/material/Button';
import AddIcon from '@mui/icons-material/Add';
import { FieldInputProps, FieldProps, FormikHelpers } from 'formik';
import { ucfirst } from '../../../util';
import { FormikProps } from 'formik/dist/types';
import PublisherModal from 'modals/PublisherModal';
import { CountryType } from 'types/CountryType';
import ClearIcon from '@mui/icons-material/Clear';
import { IconButton } from '@mui/material';
import { useNotifications } from 'components/WithNotifications';

interface AgentPickerPropsProps extends FieldProps {
    serverError?: any;
    countries: CountryType[];
}

const handleAdd = (agent, field: FieldInputProps<any>, form: FormikProps<any> & FormikHelpers<any>, notifications) => {
    const newData = field.value;

    const exists =
        agent.id !== '' && agent.id === null
            ? !!newData.find(existingAgent => {
                if (existingAgent.type !== agent.type) {
                    return false;
                }

                return existingAgent[existingAgent.type].id === agent[agent.type].id;
            })
            : false;

    if (!exists) {
        newData.push(agent);
    } else {
        notifications.show('The agent was already associated with this metadata and was, therefore, not added again.', { variant: 'error' });
    }

    form.setFieldValue(field.name, newData);
};

const handleRemove = (field: FieldInputProps<any>, form: FormikProps<any> & FormikHelpers<any>, index: number) => {
    let newData = field.value;
    newData.splice(index, 1);

    form.setFieldValue(field.name, newData);
};

const AgentPicker: FC<AgentPickerPropsProps> = ({ field, form, countries, serverError }) => {
    const [showModal, setShowModal] = useState(false);
    const notifications = useNotifications();

    const serverErrors = serverError[field.name];
    const value = field.value ? field.value : [];

    return (
        <div className="Input AgentPicker">
            <PublisherModal
                open={showModal}
                onClose={() => setShowModal(false)}
                handleSave={agent => {
                    handleAdd(agent, field, form, notifications);
                    setShowModal(false);
                }}
                countries={countries}
            />

            <div className="Header Row">
                <div className="Name">Name</div>
                <div className="Type">Type</div>
                <div className="AdditionalInformation">Additional information</div>
            </div>
            <div className="Agents">
                {value.map((agent, index) => {
                    let name = '';
                    let additionalInfo = '';

                    if (agent.type === 'organization') {
                        name = agent.organization.name;
                        additionalInfo = typeof agent.department !== 'undefined' ? agent.department.name : '';
                    } else if (agent.type === 'person') {
                        name = [agent.person.firstName, agent.person.middleName, agent.person.lastName].filter(Boolean).join(' ');
                        additionalInfo = agent.person.orcid;
                    }

                    return (
                        <div className="Row" key={index}>
                            <div className="Name">{name}</div>
                            <div className="Type">{ucfirst(agent.type)}</div>
                            <div className="AdditionalInformation">{additionalInfo}</div>
                            <div className="Buttons">
                                <IconButton
                                    className="RemoveButton"
                                    onClick={() => handleRemove(field, form, index)}
                                >
                                    <ClearIcon />
                                </IconButton>
                            </div>
                        </div>
                    );
                })}
            </div>
            <div className="AddButton">
                <Button startIcon={<AddIcon />} className="AddButton" variant="text" onClick={() => setShowModal(true)}>
                    Add
                </Button>
            </div>
        </div>
    );
};

export default AgentPicker;

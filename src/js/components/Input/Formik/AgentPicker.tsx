import React, { FC, useState } from 'react';
import {
    Box,
    Button,
    IconButton,
    Paper,
    Table,
    TableBody,
    TableCell,
    TableContainer,
    TableHead,
    TableRow,
    Typography,
} from '@mui/material';
import AddIcon from '@mui/icons-material/Add';
import ClearIcon from '@mui/icons-material/Clear';
import { FieldInputProps, FieldProps, FormikHelpers } from 'formik';
import { FormikProps } from 'formik/dist/types';
import { ucfirst } from '../../../util';
import PublisherModal from 'modals/PublisherModal';
import { CountryType } from 'types/CountryType';
import { useNotifications } from 'components/WithNotifications';

interface AgentPickerPropsProps extends FieldProps {
    serverError?: any;
    countries: CountryType[];
    label: string;
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

const AgentPicker: FC<AgentPickerPropsProps> = ({ field, form, countries, serverError, label }) => {
    const [showModal, setShowModal] = useState(false);
    const notifications = useNotifications();

    const serverErrors = serverError[field.name];
    const value = field.value ? field.value : [];

    return (
        <Box sx={{ width: '100%' }}>
            <PublisherModal
                open={showModal}
                label={label}
                onClose={() => setShowModal(false)}
                handleSave={agent => {
                    handleAdd(agent, field, form, notifications);
                    setShowModal(false);
                }}
                countries={countries}
            />

            <TableContainer
                component={Paper}
                variant="outlined"
                sx={{
                    mb: 2,
                    '& .MuiTableCell-root': {
                        py: 1.5,
                    },
                }}
            >
                <Table size="small">
                    <TableHead>
                        <TableRow>
                            <TableCell sx={{ fontWeight: 500 }}>Name</TableCell>
                            <TableCell sx={{ fontWeight: 500 }}>Type</TableCell>
                            <TableCell sx={{ fontWeight: 500 }}>Additional information</TableCell>
                            <TableCell align="right" sx={{ width: 70 }} />
                        </TableRow>
                    </TableHead>
                    <TableBody>
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
                                <TableRow
                                    key={index}
                                    sx={{
                                        '&:last-child td, &:last-child th': { border: 0 },
                                        '&:hover': {
                                            bgcolor: 'action.hover',
                                        },
                                    }}
                                >
                                    <TableCell>{name}</TableCell>
                                    <TableCell>{ucfirst(agent.type)}</TableCell>
                                    <TableCell>{additionalInfo}</TableCell>
                                    <TableCell align="right">
                                        <IconButton
                                            size="small"
                                            onClick={() => handleRemove(field, form, index)}
                                            sx={{
                                                color: 'error.main',
                                                '&:hover': {
                                                    bgcolor: 'error.lighter',
                                                },
                                            }}
                                        >
                                            <ClearIcon fontSize="small" />
                                        </IconButton>
                                    </TableCell>
                                </TableRow>
                            );
                        })}
                        {value.length === 0 && (
                            <TableRow>
                                <TableCell colSpan={4} align="center" sx={{ py: 4 }}>
                                    <Typography color="text.secondary">
                                        No agents added yet
                                    </Typography>
                                </TableCell>
                            </TableRow>
                        )}
                    </TableBody>
                </Table>
            </TableContainer>

            <Box sx={{ display: 'flex', justifyContent: 'flex-start' }}>
                <Button
                    startIcon={<AddIcon />}
                    variant="outlined"
                    onClick={() => setShowModal(true)}
                    sx={{ px: 3 }}
                >
                    Add new
                </Button>
            </Box>
        </Box>
    );
};

export default AgentPicker;

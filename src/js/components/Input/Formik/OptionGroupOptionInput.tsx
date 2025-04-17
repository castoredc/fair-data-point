import React, { FC } from 'react';

import Button from '@mui/material/Button';
import AddIcon from '@mui/icons-material/Add';
import { FieldInputProps, FieldProps, FormikHelpers } from 'formik';
import FieldErrors from 'components/Input/Formik/Errors';
import { replaceAt } from '../../../util';
import { FormikProps } from 'formik/dist/types';
import ClearIcon from '@mui/icons-material/Clear';
import { IconButton, Paper, Table, TableBody, TableCell, TableHead, TableRow, TextField } from '@mui/material';

interface OptionGroupOptionInputProps extends FieldProps {
    serverError?: any;
    multiline?: boolean;
}

const handleChange = (field: FieldInputProps<any>, form: FormikProps<any> & FormikHelpers<any>, index: number, name: string, value: string) => {
    const data = field.value ? field.value : [defaultData];

    const newData = replaceAt(data, index, {
        ...data[index],
        [name]: value,
    });

    form.setFieldValue(field.name, newData);
};

const handleAdd = (field: FieldInputProps<any>, form: FormikProps<any> & FormikHelpers<any>) => {
    const newData = field.value ? field.value : [defaultData];
    newData.push(defaultData);

    form.setFieldValue(field.name, newData);
};

const handleRemove = (field: FieldInputProps<any>, form: FormikProps<any> & FormikHelpers<any>, index: number) => {
    let newData = field.value ? field.value : [defaultData];
    newData.splice(index, 1);

    form.setFieldValue(field.name, newData);
};

const OptionGroupOptionInput: FC<OptionGroupOptionInputProps> = ({ field, form, multiline, serverError }) => {
    const serverErrors = serverError[field.name];
    const value = field.value ? field.value : [defaultData];

    return (
        <div className="Input OptionGroupOptionInput">
            <Paper elevation={0}>
                <Table size="small">
                    <TableHead>
                        <TableRow>
                            <TableCell>Name</TableCell>
                            <TableCell>Value</TableCell>
                            <TableCell width={48} /> {/* Column for action buttons */}
                        </TableRow>
                    </TableHead>
                    <TableBody>
                        {value.map((option, index) => {
                            const first = index === 0;

                            return (
                                <React.Fragment key={`${field.name}-${index}`}>
                                    <TableRow>
                                        <TableCell>
                                            <TextField
                                                fullWidth
                                                size="small"
                                                name="title"
                                                onChange={e => {
                                                    handleChange(field, form, index, 'title', e.target.value);
                                                }}
                                                value={option.title}
                                            />
                                        </TableCell>
                                        <TableCell>
                                            <TextField
                                                fullWidth
                                                size="small"
                                                name="value"
                                                onChange={e => {
                                                    handleChange(field, form, index, 'value', e.target.value);
                                                }}
                                                value={option.value}
                                            />
                                        </TableCell>
                                        <TableCell padding="none">
                                            {!first && (
                                                <IconButton
                                                    size="small"
                                                    onClick={() => handleRemove(field, form, index)}
                                                >
                                                    <ClearIcon />
                                                </IconButton>
                                            )}
                                        </TableCell>
                                    </TableRow>
                                    {serverErrors && (
                                        <TableRow>
                                            <TableCell colSpan={3}>
                                                <FieldErrors field={field} serverErrors={serverErrors} index={index} />
                                            </TableCell>
                                        </TableRow>
                                    )}
                                </React.Fragment>
                            );
                        })}
                    </TableBody>
                </Table>
            </Paper>

            <div style={{ marginTop: '16px' }}>
                <Button
                    startIcon={<AddIcon />}
                    variant="text"
                    onClick={() => handleAdd(field, form)}
                >
                    Add new
                </Button>
            </div>
        </div>
    );
};

export default OptionGroupOptionInput;

const defaultData = {
    title: '',
    value: '',
};

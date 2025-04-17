import React, { FC, Fragment } from 'react';

import Button from '@mui/material/Button';
import AddIcon from '@mui/icons-material/Add';
import { FieldInputProps, FieldProps, FormikHelpers } from 'formik';
import FieldErrors from 'components/Input/Formik/Errors';
import { replaceAt } from '../../../util';
import { FormikProps } from 'formik/dist/types';
import ClearIcon from '@mui/icons-material/Clear';
import { Autocomplete, IconButton, TextField } from '@mui/material';
import Stack from '@mui/material/Stack';

interface LocalizedTextInputProps extends FieldProps {
    languages: any;
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

const LocalizedTextInput: FC<LocalizedTextInputProps> = ({ field, form, languages, multiline, serverError }) => {
    const serverErrors = serverError ? serverError[field.name] : undefined;

    const value = field.value && field.value.length > 0 ? field.value : [defaultData];

    return (
        <div className="Input LocalizedTextInput">
            <div className="LocalizedTextInputItems">
                {value.map((localizedTextItem, index) => {
                    const first = index === 0;

                    return (
                        <Fragment key={`${field.name}-${index}`}>
                            <Stack direction="row" spacing={2}>
                                <TextField
                                    name="text"
                                    variant="outlined"
                                    onChange={e => {
                                        handleChange(field, form, index, 'text', e.target.value);
                                    }}
                                    value={localizedTextItem.text}
                                    multiline={multiline}
                                    minRows={multiline ? 3 : undefined}
                                    sx={{ width: 400 }}
                                    // rows={rows}
                                />
                                <Autocomplete
                                    options={languages}
                                    renderInput={(params) => <TextField {...params} />}
                                    onChange={(event: any, newValue) => {
                                        handleChange(field, form, index, 'language', newValue.value);
                                    }}
                                    value={languages.find(language => language.value === localizedTextItem.language) ?? null}
                                    disableClearable={true}
                                    sx={{ width: 140 }}
                                />
                                <div className="LocalizedTextInputButtons">
                                    <div className="LocalizedTextInputButton">
                                        {!first && (
                                            <IconButton
                                                className="RemoveButton"
                                                onClick={() => handleRemove(field, form, index)}
                                            >
                                                <ClearIcon />
                                            </IconButton>
                                        )}
                                    </div>
                                </div>
                            </Stack>
                            <FieldErrors field={field} serverErrors={serverErrors} index={index} />
                        </Fragment>
                    );
                })}
            </div>

            <div className="LocalizedTextInputAddButton">
                <Button
                    startIcon={<AddIcon />}
                    className="AddButton"
                    variant="text"
                    onClick={() => handleAdd(field, form)}
                >
                    Add new
                </Button>
            </div>
        </div>
    );
};

export default LocalizedTextInput;

const defaultData = {
    text: '',
    language: '',
};

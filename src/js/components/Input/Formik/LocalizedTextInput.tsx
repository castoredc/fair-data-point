import React, { FC, Fragment } from 'react';

import { Button, DefaultOptionType, Dropdown, TextInput } from '@castoredc/matter';
import { FieldInputProps, FieldProps, FormikHelpers } from 'formik';
import FieldErrors from 'components/Input/Formik/Errors';
import { replaceAt } from '../../../util';
import { FormikProps } from 'formik/dist/types';

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

    const value = (field.value && field.value.length > 0) ? field.value : [defaultData];

    return (
        <div className="Input LocalizedTextInput">
            <div className="LocalizedTextInputItems">
                {value.map((localizedTextItem, index) => {
                    const first = index === 0;

                    return (
                        <Fragment key={`${field.name}-${index}`} >
                            <div className="LocalizedTextInputItem">
                                <div className="LocalizedTextInputText">
                                    <TextInput
                                        name="text"
                                        onChange={e => {
                                            handleChange(field, form, index, 'text', e.target.value);
                                        }}
                                        value={localizedTextItem.text}
                                        multiline={multiline}
                                        // rows={rows}
                                    />
                                </div>
                                <div className="LocalizedTextInputLanguage">
                                    <Dropdown
                                        options={languages}
                                        menuPlacement={'auto'}
                                        getOptionLabel={({ label }) => label}
                                        getOptionValue={({ value }) => value}
                                        onChange={(option: DefaultOptionType) => {
                                            handleChange(field, form, index, 'language', option.value);
                                        }}
                                        value={languages.find(language => language.value === localizedTextItem.language)}
                                        width="minimum"
                                    />
                                </div>
                                <div className="LocalizedTextInputButtons">
                                    <div className="LocalizedTextInputButton">
                                        {!first && (
                                            <Button
                                                icon="cross"
                                                className="RemoveButton"
                                                buttonType="contentOnly"
                                                onClick={() => handleRemove(field, form, index)}
                                                iconDescription="Remove text"
                                            />
                                        )}
                                    </div>
                                </div>
                            </div>
                            <FieldErrors field={field} serverErrors={serverErrors} index={index} />
                        </Fragment>
                    );
                })}
            </div>

            <div className="LocalizedTextInputAddButton">
                <Button icon="add" className="AddButton" buttonType="bare" onClick={() => handleAdd(field, form)}>
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

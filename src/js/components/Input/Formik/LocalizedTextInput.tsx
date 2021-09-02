import React, {FC} from 'react'

import {Button, Dropdown, TextInput} from "@castoredc/matter";
import {FieldInputProps, FieldProps, FormikHelpers, FormikSharedConfig} from "formik";
import FieldErrors from "components/Input/Formik/Errors";
import {replaceAt} from "../../../util";
import {FormikProps} from "formik/dist/types";
import {ActionMeta, ValueType} from "react-select/src/types";
import {OptionType} from "components/Input/Formik/Select";

interface LocalizedTextInputProps extends FieldProps {
    languages: any,
    serverError?: any,
    multiline?: boolean,
}

const handleChange = (field: FieldInputProps<any>, form: FormikProps<any> & FormikHelpers<any>, index: number, name: string, value: string) => {
    const newData = replaceAt(field.value, index, {
        ...field.value[index],
        [name]: value,
    });

    form.setFieldValue(field.name, newData);
};

const handleAdd = (field: FieldInputProps<any>, form: FormikProps<any> & FormikHelpers<any>) => {
    const newData = field.value;
    newData.push(defaultData);

    form.setFieldValue(field.name, newData);
};

const handleRemove = (field: FieldInputProps<any>, form: FormikProps<any> & FormikHelpers<any>, index: number) => {
    let newData = field.value;
    newData.splice(index, 1);

    form.setFieldValue(field.name, newData);
};

const LocalizedTextInput: FC<LocalizedTextInputProps> = ({field, form, languages, multiline, serverError}) => {
    const serverErrors = serverError[field.name];

    return <div className="Input LocalizedTextInput">
        <div className="LocalizedTextInputItems">
            {field.value.map((localizedTextItem, index) => {
                const first = index === 0;

                return <div key={`${field.name}-${index}`} className="LocalizedTextInputItem">
                    <div className="LocalizedTextInputText">
                        <TextInput
                            name="text"
                            onChange={(e) => { handleChange(field, form, index, 'text', e.target.value)}}
                            value={localizedTextItem.text}
                            multiline={multiline}
                            // rows={rows}
                        />
                    </div>
                    <div className="LocalizedTextInputLanguage">
                        <Dropdown
                            options={languages}
                            menuPlacement={"auto"}
                            getOptionLabel={({label}) => label }
                            getOptionValue={({value}) => value }
                            onChange={(option: OptionType) => {
                                handleChange(field, form, index, 'language', option.value)}
                            }
                            value={languages.find((language) => language.value === localizedTextItem.language)}
                            width="minimum"
                        />
                    </div>
                    <div className="LocalizedTextInputButtons">
                        <div className="LocalizedTextInputButton">
                            {!first && <Button icon="cross" className="RemoveButton" buttonType="contentOnly" onClick={() => handleRemove(field, form, index)} iconDescription="Remove text" />}
                        </div>
                    </div>
                </div>
            })}
        </div>

        <div className="LocalizedTextInputAddButton">
            <Button icon="add" className="AddButton" buttonType="contentOnly" onClick={() => handleAdd(field, form)}>Add new</Button>
        </div>

        <FieldErrors field={field} serverErrors={serverErrors}/>
    </div>;
}

export default LocalizedTextInput;

const defaultData = {
    text:     '',
    language: null
};
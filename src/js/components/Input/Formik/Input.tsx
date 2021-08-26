import React, {ChangeEvent, FC} from 'react'

import {Space, TextInput, ValidationMessage} from "@castoredc/matter";
import {ErrorMessage, FieldProps} from "formik";

interface InputProps extends FieldProps {
    readOnly?: boolean,
    onChange?: (event: ChangeEvent<HTMLInputElement>) => void,
    autoFocus?: boolean,
    serverError?: any
}

const Input: FC<InputProps> = ({field, form, meta, readOnly, onChange, autoFocus, serverError}) => {
    const touched = form.touched[field.name];
    const errors = form.errors[field.name];
    const serverErrors = serverError[field.name];

    return <>
        <TextInput
            name={field.name}
            value={field.value}
            onChange={onChange ? (event) => {
                onChange(event);
                field.onChange(event)
            } : field.onChange}
            onBlur={field.onBlur}
            invalid={touched && !!errors}
            readOnly={readOnly}
            autoFocus={autoFocus}
        />

        <ErrorMessage
            name={field.name}
            render={msg => <>
                <Space bottom="default"/>
                <ValidationMessage type="error">{msg}</ValidationMessage>
            </>}
        />

        {serverErrors && <ValidationMessage type="error">
            {serverErrors.map((errorText, index) => (
                <div key={index}>
                    {errorText}
                </div>
            ))}
        </ValidationMessage>}
    </>;
}

export default Input;

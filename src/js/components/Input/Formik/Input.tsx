import React, {ChangeEvent, FC} from 'react'

import {TextInput} from "@castoredc/matter";
import {FieldProps} from "formik";
import FieldErrors from "components/Input/Formik/Errors";

interface InputProps extends FieldProps {
    readOnly?: boolean,
    onChange?: (event: ChangeEvent<HTMLInputElement>) => void,
    autoFocus?: boolean,
    serverError?: any,
    multiline?: boolean
}

const Input: FC<InputProps> = ({field, form, meta, readOnly, onChange, autoFocus, serverError, multiline}) => {
    const touched = form.touched[field.name];
    const errors = form.errors[field.name];
    const serverErrors = serverError ? serverError[field.name] : undefined;

    return <>
        <TextInput
            name={field.name}
            value={field.value ?? ''}
            onChange={onChange ? (event) => {
                onChange(event);
                field.onChange(event)
            } : field.onChange}
            onBlur={field.onBlur}
            invalid={touched && !!errors}
            readOnly={readOnly}
            autoFocus={autoFocus}
            multiline={multiline}
        />

        <FieldErrors field={field} serverErrors={serverErrors}/>
    </>;
}

export default Input;

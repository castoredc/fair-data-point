import React, { FC } from 'react';
import { FieldProps } from 'formik';
import FieldErrors from 'components/Input/Formik/Errors';
import TextField from '@mui/material/TextField';
import { FilledTextFieldProps } from '@mui/material/TextField/TextField';

interface InputProps extends FieldProps, FilledTextFieldProps {
    readOnly?: boolean;
    autoFocus?: boolean;
    serverError?: any;
    multiline?: boolean;
    inputMode?: 'none' | 'text' | 'tel' | 'url' | 'email' | 'numeric' | 'decimal' | 'search' | undefined;
}

const Input: FC<InputProps> = ({
                                   field,
                                   form,
                                   meta,
                                   readOnly,
                                   onChange,
                                   autoFocus,
                                   serverError,
                                   multiline,
                                   inputMode,
                               }) => {
    const touched = form.touched[field.name];
    const errors = form.errors[field.name];
    const serverErrors = serverError ? serverError[field.name] : undefined;

    let slotProps = {};
    if (readOnly) {
        slotProps = {
            input: {
                readOnly: true,
            },
        };
    }

    return (
        <>
            <TextField
                name={field.name}
                value={field.value ?? ''}
                onChange={
                    onChange
                        ? event => {
                            onChange(event);
                            field.onChange(event);
                        }
                        : field.onChange
                }
                onBlur={field.onBlur}
                // invalid={touched && !!errors}
                slotProps={slotProps}
                autoFocus={autoFocus}
                multiline={multiline}
                inputMode={inputMode}
                sx={{ width: 400 }}
            />

            <FieldErrors field={field} serverErrors={serverErrors} />
        </>
    );
};

export default Input;

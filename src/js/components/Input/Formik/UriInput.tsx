import React, { ChangeEvent, FC } from 'react';
import { FieldProps } from 'formik';
import FieldErrors from 'components/Input/Formik/Errors';
import { PrefixType } from 'types/PrefixType';
import { TextField } from '@mui/material';

interface UriInputProps extends FieldProps {
    readOnly?: boolean;
    onChange?: (event: ChangeEvent<HTMLInputElement | HTMLTextAreaElement>) => void;
    autoFocus?: boolean;
    serverError?: any;
    multiline?: boolean;
    prefixes: PrefixType[];
    fullWidth?: boolean;
}

const UriInput: FC<UriInputProps> = ({
                                         field,
                                         form,
                                         meta,
                                         readOnly,
                                         onChange,
                                         autoFocus,
                                         serverError,
                                         multiline,
                                         prefixes,
                                         fullWidth,
                                     }) => {
    const touched = form.touched[field.name];
    const errors = form.errors[field.name];
    const serverErrors = serverError ? serverError[field.name] : undefined;

    return (
        <>
            <TextField
                name={field.name}
                value={field.value ?? ''}
                onChange={event => {
                    let newEvent = event;
                    let predicate = event.target.value;
                    const regex = /^([^:]*):(.*)/;
                    const matches = regex.exec(predicate);

                    if (matches !== null) {
                        const matchedPrefix = matches[1];
                        const foundPrefix = prefixes.find(prefix => {
                            return prefix.prefix === matchedPrefix;
                        });

                        if (typeof foundPrefix !== 'undefined') {
                            predicate = foundPrefix.uri + matches[2];
                        }
                    }

                    newEvent.target.value = predicate;

                    if (onChange) {
                        onChange(newEvent);
                    }

                    field.onChange(newEvent);
                }}
                onBlur={field.onBlur}
                // invalid={touched && !!errors}
                // readOnly={readOnly}
                autoFocus={autoFocus}
                multiline={multiline}
                fullWidth={fullWidth}
            />

            <FieldErrors field={field} serverErrors={serverErrors} />
        </>
    );
};

export default UriInput;

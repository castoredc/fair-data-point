import React, { ChangeEvent, FC } from 'react';

import { FieldProps } from 'formik';
import FieldErrors from 'components/Input/Formik/Errors';
import { TimePicker as CastorTimePicker } from '@castoredc/matter';

interface TimePickerProps extends FieldProps {
    readOnly?: boolean;
    onChange?: (event: ChangeEvent<HTMLInputElement>) => void;
    serverError?: any;
}

const TimePicker: FC<TimePickerProps> = ({ field, form, meta, readOnly, onChange, serverError }) => {
    const touched = form.touched[field.name];
    const errors = form.errors[field.name];
    const serverErrors = serverError ? serverError[field.name] : undefined;

    const value = field.value !== '' && field.value !== null ? field.value : undefined;

    return (
        <>
            <CastorTimePicker
                name={field.name}
                selected={value}
                onChange={(date, event) => {
                    field.onChange({ target: { name: field.name, value: date } });
                }}
                onBlur={field.onBlur}
                invalid={touched && !!errors}
                readOnly={readOnly}
            />

            <FieldErrors field={field} serverErrors={serverErrors} />
        </>
    );
};

export default TimePicker;

import React, { ChangeEvent, FC } from 'react';

import { FieldProps } from 'formik';
import FieldErrors from 'components/Input/Formik/Errors';
import { TimePicker as MuiTimePicker } from '@mui/x-date-pickers';
import { AdapterMoment } from '@mui/x-date-pickers/AdapterMoment';
import { LocalizationProvider } from '@mui/x-date-pickers';

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
        <LocalizationProvider dateAdapter={AdapterMoment}>
            <MuiTimePicker
                name={field.name}
                value={value}
                onChange={(date, event) => {
                    field.onChange({ target: { name: field.name, value: date } });
                }}
                readOnly={readOnly}
            />

            <FieldErrors field={field} serverErrors={serverErrors} />
        </LocalizationProvider>
    );
};

export default TimePicker;

import React, { ChangeEvent, FC } from 'react';

import { FieldProps } from 'formik';
import FieldErrors from 'components/Input/Formik/Errors';
import { DatePicker as MuiDatePicker } from '@mui/x-date-pickers';
import { format } from 'date-fns';
import { AdapterMoment } from '@mui/x-date-pickers/AdapterMoment';
import { LocalizationProvider } from '@mui/x-date-pickers';
import moment from 'moment';

interface DatePickerProps extends FieldProps {
    readOnly?: boolean;
    onChange?: (event: ChangeEvent<HTMLInputElement>) => void;
    serverError?: any;
}

const DatePicker: FC<DatePickerProps> = ({
                                             field,
                                             form,
                                             meta,
                                             readOnly,
                                             onChange,
                                             serverError,
                                         }) => {
    const touched = form.touched[field.name];
    const errors = form.errors[field.name];
    const serverErrors = serverError ? serverError[field.name] : undefined;

    const value = (field.value !== null && field.value !== '') ? moment(field.value) : null;
    console.log(value);

    return (
        <LocalizationProvider dateAdapter={AdapterMoment}>
            <MuiDatePicker
                name={field.name}
                value={value}
                onChange={(newValue) => {
                    const formattedDate = newValue?.format('YYYY-MM-DD');
                    console.log(formattedDate);
                    field.onChange({ target: { name: field.name, value: formattedDate } });
                }}
                readOnly={readOnly}
            />

            <FieldErrors field={field} serverErrors={serverErrors} />
        </LocalizationProvider>
    );
};

export default DatePicker;

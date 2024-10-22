import React, { ChangeEvent, FC } from 'react';

import { FieldProps } from 'formik';
import FieldErrors from 'components/Input/Formik/Errors';
import { DatePicker as CastorDatePicker } from '@castoredc/matter';
import { format } from 'date-fns';

interface DatePickerProps extends FieldProps {
    readOnly?: boolean;
    onChange?: (event: ChangeEvent<HTMLInputElement>) => void;
    serverError?: any;
    showMonthDropdown?: boolean;
    showYearDropdown?: boolean;
}

const DatePicker: FC<DatePickerProps> = ({ field, form, meta, readOnly, onChange, serverError, showMonthDropdown, showYearDropdown }) => {
    const touched = form.touched[field.name];
    const errors = form.errors[field.name];
    const serverErrors = serverError ? serverError[field.name] : undefined;

    const value = field.value !== null ? field.value : '';

    return (
        <>
            <CastorDatePicker
                name={field.name}
                selected={value !== '' ? new Date(value) : null}
                dateFormat="dd-MM-yyyy"
                onChange={(date, event) => {
                    const formattedDate = format(date, 'yyyy-MM-dd');
                    field.onChange({ target: { name: field.name, value: formattedDate } });
                }}
                onBlur={field.onBlur}
                invalid={touched && !!errors}
                readOnly={readOnly}
                showYearDropdown={showYearDropdown}
                showMonthDropdown={showMonthDropdown}
            />

            <FieldErrors field={field} serverErrors={serverErrors} />
        </>
    );
};

export default DatePicker;

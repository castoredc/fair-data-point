import React, {ChangeEvent, FC} from 'react'

import {FieldProps} from "formik";
import FieldErrors from "components/Input/Formik/Errors";
import {DatePicker as CastorDatePicker} from "@castoredc/matter";

interface DatePickerProps extends FieldProps {
    readOnly?: boolean,
    onChange?: (event: ChangeEvent<HTMLInputElement>) => void,
    serverError?: any,
    showMonthDropdown?: boolean,
    showYearDropdown?: boolean
}

const DatePicker: FC<DatePickerProps> = ({
                                             field, form, meta, readOnly, onChange, serverError,
                                             showMonthDropdown,
                                             showYearDropdown
                                         }) => {
    const touched = form.touched[field.name];
    const errors = form.errors[field.name];
    const serverErrors = serverError ? serverError[field.name] : undefined;

    const value = (field.value !== '' && field.value !== null) ? field.value : undefined;

    return <>
        <CastorDatePicker
            name={field.name}
            selected={value}
            dateFormat="DD-MM-YYYY"
            onChange={(date) => {
                if (onChange) {
                    onChange(date);
                }
                field.onChange({target: {name: field.name, value: date}});
            }}
            onBlur={field.onBlur}
            invalid={touched && !!errors}
            readOnly={readOnly}
            showYearDropdown={showYearDropdown}
            showMonthDropdown={showMonthDropdown}
        />

        <FieldErrors field={field} serverErrors={serverErrors}/>
    </>;
}

export default DatePicker;

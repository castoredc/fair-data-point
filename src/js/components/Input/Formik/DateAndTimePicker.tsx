import React, { ChangeEvent, FC } from 'react';

import { FieldProps } from 'formik';
import FieldErrors from 'components/Input/Formik/Errors';
import { DatePicker as CastorDatePicker, Stack, TimePicker as CastorTimePicker } from '@castoredc/matter';
import moment from 'moment';
import { format, parseISO, setHours, setMinutes, setSeconds } from 'date-fns';

interface DateAndTimePickerProps extends FieldProps {
    readOnly?: boolean;
    onChange?: (event: ChangeEvent<HTMLInputElement>) => void;
    serverError?: any;
}

const DateAndTimePicker: FC<DateAndTimePickerProps> = ({ field, form, meta, readOnly, onChange, serverError }) => {
    const touched = form.touched[field.name];
    const errors = form.errors[field.name];
    const serverErrors = serverError ? serverError[field.name] : undefined;

    const value = field.value !== '' && field.value !== null ? field.value : undefined;

    const { date, time } = {
        date: value ? value.split(';')[0] : '',
        time: value ? value.split(';')[1] : '',
    };

    return (
        <>
            <Stack>
                <CastorDatePicker
                    name={`${field.name}_date`}
                    selected={date !== '' ? new Date(date) : null}
                    dateFormat="dd-MM-yyyy"
                    onChange={(date, event) => {
                        const formattedDate = format(date, 'yyyy-MM-dd')
                        field.onChange({ target: { name: field.name, value: `${formattedDate};${time}` } });
                    }}
                    onBlur={field.onBlur}
                    invalid={touched && !!errors}
                    readOnly={readOnly}
                />

                <CastorTimePicker
                    name={`${field.name}_time`}
                    selected={
                        time !== '' &&
                        setSeconds(
                            setMinutes(
                                setHours(new Date(), Number(time.split(':')[0])),
                                Number(time.split(':')[1])
                            ),
                            0
                        )
                    }
                    onChange={(time, event) => {
                        const formattedTime = format(time, 'HH:mm')
                        field.onChange({ target: { name: field.name, value: `${date};${formattedTime}` } });
                    }}
                    onBlur={field.onBlur}
                    invalid={touched && !!errors}
                    readOnly={readOnly}
                />
            </Stack>

            <FieldErrors field={field} serverErrors={serverErrors} />
        </>
    );
};

export default DateAndTimePicker;

import React, { ChangeEvent, FC } from 'react';

import { FieldProps } from 'formik';
import FieldErrors from 'components/Input/Formik/Errors';
import { format, setHours, setMinutes, setSeconds, parseISO } from 'date-fns';
import Stack from '@mui/material/Stack';
import { DatePicker, TimePicker } from '@mui/x-date-pickers';
import { AdapterMoment } from '@mui/x-date-pickers/AdapterMoment';
import { LocalizationProvider } from '@mui/x-date-pickers';
import moment from 'moment';

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
        <LocalizationProvider dateAdapter={AdapterMoment}>
            <Stack direction="row">
                <DatePicker
                    name={`${field.name}_date`}
                    value={date ? moment(date) : null}
                    onChange={(newDate) => {
                        if (newDate) {
                            const formattedDate = format(newDate.toDate(), 'yyyy-MM-dd');
                            field.onChange({ target: { name: field.name, value: `${formattedDate};${time}` } });
                        }
                    }}
                    readOnly={readOnly}
                />

                <TimePicker
                    name={`${field.name}_time`}
                    value={time ? moment(setSeconds(setMinutes(setHours(new Date(), Number(time.split(':')[0])), Number(time.split(':')[1])), 0)) : null}
                    onChange={(newTime) => {
                        if (newTime) {
                            const formattedTime = format(newTime.toDate(), 'HH:mm');
                            field.onChange({ target: { name: field.name, value: `${date};${formattedTime}` } });
                        }
                    }}
                    readOnly={readOnly}
                />
            </Stack>

            <FieldErrors field={field} serverErrors={serverErrors} />
        </LocalizationProvider>
    );
};

export default DateAndTimePicker;

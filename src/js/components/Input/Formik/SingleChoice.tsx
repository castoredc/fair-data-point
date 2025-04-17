import React, { FC, FormEvent, ReactNode } from 'react';
import { FieldProps } from 'formik';
import FieldErrors from 'components/Input/Formik/Errors';
import { Checkbox, FormControlLabel } from '@mui/material';

interface SingleChoiceProps extends FieldProps {
    readOnly?: boolean;
    onChange?: (event: FormEvent<HTMLInputElement>) => void;
    serverError?: any;
    label: string;
    details?: ReactNode;
}

const SingleChoice: FC<SingleChoiceProps> = ({ field, readOnly, onChange, label, serverError, details }) => {
    const serverErrors = serverError ? serverError[field.name] : undefined;

    return (
        <>
            <FormControlLabel
                control={
                    <Checkbox
                        checked={field.value}
                        name={field.name}
                        onChange={
                            onChange
                                ? event => {
                                    onChange(event);
                                    field.onChange(event);
                                }
                                : field.onChange
                        }
                        disabled={readOnly}
                    />
                }
                label={<>
                    {label}
                    {details && <div>{details}</div>}
                </>}
            />

            <FieldErrors field={field} serverErrors={serverErrors} />
        </>
    );
};

export default SingleChoice;

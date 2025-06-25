import React, { FC, FormEvent, ReactNode } from 'react';

import { ChoiceOption } from '@castoredc/matter';
import { FieldProps } from 'formik';
import FieldErrors from 'components/Input/Formik/Errors';

interface SingleChoiceProps extends FieldProps {
    readOnly?: boolean;
    onChange?: (event: FormEvent<HTMLInputElement>) => void;
    serverError?: any;
    labelText: string;
    details?: ReactNode;
}

const SingleChoice: FC<SingleChoiceProps> = ({ field, readOnly, onChange, serverError, labelText, details }) => {
    const serverErrors = serverError ? serverError[field.name] : undefined;

    return (
        <>
            <ChoiceOption
                checked={field.value}
                labelText={labelText}
                name={field.name}
                details={details}
                type="checkbox"
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

            <FieldErrors field={field} serverErrors={serverErrors} />
        </>
    );
};

export default SingleChoice;

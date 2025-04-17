import React, { FC, FormEvent } from 'react';
import { FieldProps } from 'formik';
import FieldErrors from 'components/Input/Formik/Errors';
import RadioGroup, { Option } from 'components/RadioGroup';

interface ChoiceProps extends FieldProps {
    readOnly?: boolean;
    onChange?: (event: FormEvent<HTMLInputElement>) => void;
    options: Option[];
    serverError?: any;
    collapse?: boolean;
    multiple?: boolean;
}

const Choice: FC<ChoiceProps> = ({ field, readOnly, onChange, options, serverError, collapse, multiple }) => {
    const serverErrors = serverError ? serverError[field.name] : undefined;

    const renderedOptions = options.map(option => {
        return {
            ...option,
            defaultChecked: multiple ? field.value.includes(option.value) : field.value === option.value,
        };
    });

    return (
        <>
            <RadioGroup
                name={field.name}
                options={renderedOptions}
                collapse={!!collapse}
                onChange={
                    onChange
                        ? event => {
                            onChange(event);
                            field.onChange(event);
                        }
                        : field.onChange
                }
                value={field.value}
                disabled={readOnly}
                multiple={multiple}
            />

            <FieldErrors field={field} serverErrors={serverErrors} />
        </>
    );
};

export default Choice;

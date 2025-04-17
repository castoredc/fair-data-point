import React, { FC } from 'react';

import { FieldProps } from 'formik';
import FieldErrors from 'components/Input/Formik/Errors';
import { Autocomplete, TextField } from '@mui/material';
import { DefaultOptionType, MultiValue, SingleValue } from 'types/DefaultOptionType';
import SearchIcon from '@mui/icons-material/Search';

type IsMulti = boolean;

interface SelectProps extends FieldProps {
    readOnly?: boolean;
    autoFocus?: boolean;
    serverError?: any;
    onChange?: (event: any, newValue: SingleValue | MultiValue) => void;
    options: DefaultOptionType[];
}

interface AsyncSelectProps extends SelectProps {
    cachedOptions: DefaultOptionType[];
    filterOptions?: (options: DefaultOptionType[], state: any) => DefaultOptionType[];
}

export function isMultipleOption<DefaultOptionType>(
    arg: MultiValue | SingleValue,
): arg is MultiValue {
    return Array.isArray(arg);
}

const Select: FC<SelectProps> = ({
                                     field,
                                     form,
                                     meta,
                                     readOnly,
                                     onChange,
                                     autoFocus,
                                     serverError,
                                     options,
                                     ...rest
                                 }) => {
    const touched = form.touched[field.name];
    const errors = form.errors[field.name];
    const serverErrors = serverError ? serverError[field.name] : undefined;

    return (
        <>
            <Autocomplete
                value={options && options.find((option: DefaultOptionType) => field.value === option.value)}
                renderInput={(params) => <TextField {...params} />}
                onChange={(event: any, newValue: SingleValue | MultiValue) => {
                    const returnValue = newValue && (isMultipleOption(newValue) ? newValue.map(rawValue => rawValue.value) : newValue.value);

                    if (onChange) {
                        onChange(event, newValue);
                    }

                    field.onChange({ target: { name: field.name, value: returnValue } });
                }}
                onBlur={field.onBlur}
                // invalid={touched && !!errors}
                options={options}
                disableClearable={true}
                sx={{ width: 400 }}
                {...rest}
            />

            <FieldErrors field={field} serverErrors={serverErrors} />
        </>
    );
};

export const AsyncSelect: FC<AsyncSelectProps> = ({
                                                      field,
                                                      form,
                                                      meta,
                                                      readOnly,
                                                      onChange,
                                                      autoFocus,
                                                      serverError,
                                                      cachedOptions,
                                                      options,
                                                      filterOptions,
                                                      ...rest
                                                  }) => {
    const touched = form.touched[field.name];
    const errors = form.errors[field.name];
    const serverErrors = serverError[field.name];

    return (
        <div className="Select">
            <Autocomplete
                filterOptions={filterOptions}
                options={cachedOptions}
                openOnFocus={true}
                renderInput={(params) => <TextField {...params} />}
                value={field.value}
                onChange={(event: any, newValue: SingleValue | MultiValue) => {
                    const returnValue = newValue && (isMultipleOption(newValue) ? newValue.map(rawValue => rawValue.value) : newValue.value);

                    if (onChange) {
                        onChange(event, newValue);
                    }

                    field.onChange({ target: { name: field.name, value: returnValue } });
                }}
                onBlur={field.onBlur}
                disableClearable={true}
                // invalid={touched && !!errors}
                sx={{ width: 400 }}
                {...rest}
            />

            <FieldErrors field={field} serverErrors={serverErrors} />
        </div>
    );
};

export const AsyncDropdownIndicator = props => {
    return (
        <div>
            <SearchIcon />
        </div>
    );
};

export default Select;

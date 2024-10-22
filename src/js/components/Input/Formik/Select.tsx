import React, { FC } from 'react';

import { DefaultOptionType, Dropdown, Icon, ReactSelectTypes } from '@castoredc/matter';
import { FieldProps } from 'formik';
import FieldErrors from 'components/Input/Formik/Errors';

type IsMulti = boolean;

interface SelectProps extends FieldProps, Omit<ReactSelectTypes.Props, "form"> {
    readOnly?: boolean;
    autoFocus?: boolean;
    serverError?: any;
}

interface AsyncSelectProps extends SelectProps {
    cachedOptions: ReactSelectTypes.Options<DefaultOptionType>;
}

type MultiValue<DefaultOptionType> = readonly DefaultOptionType[];
type SingleValue<DefaultOptionType> = DefaultOptionType;

export function isMultipleOption<DefaultOptionType>(arg: MultiValue<DefaultOptionType> | SingleValue<DefaultOptionType>): arg is MultiValue<DefaultOptionType> {
    return Array.isArray(arg);
}

const Select: FC<SelectProps> = ({ field, form, meta, readOnly, onChange, autoFocus, serverError, options, ...rest }) => {
    const touched = form.touched[field.name];
    const errors = form.errors[field.name];
    const serverErrors = serverError ? serverError[field.name] : undefined;

    return (
        <>
            <Dropdown
                value={options && options.find((option: DefaultOptionType) => field.value === option.value)}
                onChange={(value: ReactSelectTypes.OnChangeValue<DefaultOptionType, IsMulti>, action: ReactSelectTypes.ActionMeta<DefaultOptionType>) => {
                    const returnValue = value && (isMultipleOption(value) ? value.map(rawValue => rawValue.value) : value.value);

                    if (onChange) {
                        onChange(value, action);
                    }

                    field.onChange({ target: { name: field.name, value: returnValue } });
                }}
                onBlur={field.onBlur}
                invalid={touched && !!errors}
                options={options}
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
    loadOptions,
    cachedOptions,
    options,
    ...rest
}) => {
    const touched = form.touched[field.name];
    const errors = form.errors[field.name];
    const serverErrors = serverError[field.name];

    return (
        <div className="Select">
            <Dropdown
                loadOptions={loadOptions}
                options={cachedOptions}
                openMenuOnClick={false}
                components={{ DropdownIndicator: AsyncDropdownIndicator }}
                placeholder=""
                value={field.value}
                onChange={(value: ReactSelectTypes.OnChangeValue<DefaultOptionType, IsMulti>, action: ReactSelectTypes.ActionMeta<DefaultOptionType>) => {
                    if (onChange) {
                        onChange(value, action);
                    }
                    field.onChange({ target: { name: field.name, value: value } });
                }}
                onBlur={field.onBlur}
                invalid={touched && !!errors}
                defaultOptions
                {...rest}
            />

            <FieldErrors field={field} serverErrors={serverErrors} />
        </div>
    );
};

export const AsyncDropdownIndicator = props => {
    return (
        <div>
            <Icon
                type="search"
            />
        </div>
    );
};

export default Select;

import React, {FC} from 'react'

import {Dropdown, Icon} from "@castoredc/matter";
import {FieldProps} from "formik";
import {ActionMeta, OptionsType, ValueType} from "react-select/src/types";
import {components} from "react-select";
import {AsyncProps} from 'react-select/async';
import FieldErrors from "components/Input/Formik/Errors";
import {BaseDropdownProps} from "@castoredc/matter/lib/types/src/Dropdown/types";

export type OptionType = {
    value: string;
    label: string;
};

type IsMulti = boolean;

interface SelectProps extends FieldProps, BaseDropdownProps {
    readOnly?: boolean,
    // onChange?: (value: ValueType<OptionType, IsMulti>, action: ActionMeta<OptionType>) => void,
    autoFocus?: boolean,
    serverError?: any,
    options: OptionsType<OptionType>,
}

interface AsyncSelectProps extends SelectProps, AsyncProps<OptionType> {
    cachedOptions: OptionsType<OptionType>,
}

export function isMultipleOption(value: OptionsType<OptionType> | OptionType): value is OptionsType<OptionType> {
    return Array.isArray(value);
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

    return <>
        <Dropdown
            value={options.find((option: OptionType) => field.value === option.value)}
            onChange={(value: ValueType<OptionType, IsMulti>, action: ActionMeta<OptionType>) => {
                const returnValue = value && (isMultipleOption(value) ? value.map(rawValue => rawValue.value) : value.value);

                if (onChange) {
                    onChange(value, action);
                }

                field.onChange({target: {name: field.name, value: returnValue}});
            }}
            onBlur={field.onBlur}
            invalid={touched && !!errors}
            options={options}
            {...rest}
        />

        <FieldErrors field={field} serverErrors={serverErrors}/>
    </>;
}

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

    return <div className="Select">
        <Dropdown
            loadOptions={loadOptions}
            options={cachedOptions}
            openMenuOnClick={false}
            components={{DropdownIndicator: AsyncDropdownIndicator}}
            placeholder=""
            value={field.value}
            onChange={(value: ValueType<OptionType, IsMulti>, action: ActionMeta<OptionType>) => {
                if (onChange) {
                    onChange(value, action);
                }
                field.onChange({target: {name: field.name, value: value}});
            }}
            onBlur={field.onBlur}
            invalid={touched && !!errors}
            defaultOptions
            // getOptionLabel={({label}) => label }
            // getOptionValue={({value}) => value }
            {...rest}
        />

        <FieldErrors field={field} serverErrors={serverErrors}/>
    </div>;
}

export const AsyncDropdownIndicator = (props) => {
    return (
        <components.DropdownIndicator {...props}>
            <Icon type="search" style={{
                transform: props.selectProps.menuIsOpen ? 'rotate(180deg)' : '',
            }}/>
        </components.DropdownIndicator>
    );
};

export default Select;
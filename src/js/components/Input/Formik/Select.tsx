import React, {FC, useEffect, useState} from 'react'

import {Dropdown, DropdownProps, dropdownStyle, Icon, Space, ValidationMessage} from "@castoredc/matter";
import {ErrorMessage, FieldInputProps, FieldProps} from "formik";
import {ActionMeta, OptionsType, OptionTypeBase, ValueType} from "react-select/src/types";
import {components} from "react-select";
import Async, {AsyncProps} from 'react-select/async';
import FieldErrors from "components/Input/Formik/Errors";

export type OptionType = {
    value: string;
    label: string;
};

type IsMulti = boolean;

interface SelectProps extends FieldProps, DropdownProps {
    readOnly?: boolean,
    onChange?: (value: ValueType<OptionType, IsMulti>, action: ActionMeta<OptionType>) => void,
    autoFocus?: boolean,
    serverError?: any,
    options: OptionsType<OptionType>,
}

interface AsyncSelectProps extends SelectProps, AsyncProps<OptionType> {
    cachedOptions: OptionsType<OptionType>,
}

function isMultipleOption(value: OptionsType<OptionType> | OptionType): value is OptionsType<OptionType> {
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
                                     menuPlacement,
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
            menuPlacement={menuPlacement ? menuPlacement : "auto"}
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
                                                      menuPlacement,
                                                      loadOptions,
                                                      cachedOptions,
                                                        options,
                                                      ...rest
                                                  }) => {
    const touched = form.touched[field.name];
    const errors = form.errors[field.name];
    const serverErrors = serverError[field.name];

    const [modalOverflow, setModalOverflow] = useState<string>('');
    const [withinModal, setWithinModal] = useState<HTMLElement | null>(null);

    useEffect(() => {
        /**
         * Check if the Dropdown component is being rendered within a Modal and append
         * the menu to the document body
         */
        const modal = document.querySelector('[aria-modal=true]');
        if (!modal) return;

        /**
         * If the component is being used on a Next.js application, we will target
         * the mounting div to avoid issues with event bubbling and React Portals
         */
        const bodyFirstChild = document.body.firstElementChild as HTMLElement;
        const isNextJs = bodyFirstChild.id === '__next';
        const targetNode = isNextJs ? bodyFirstChild : document.body;

        setWithinModal(targetNode);

        // Prevent scrolling on a scrollable container when the dropdown menu is open
        const scrollableContainer = modal.querySelector<HTMLDivElement>('[data-scrollable-container]');
        if (!scrollableContainer) return;

        scrollableContainer.style.overflow = modalOverflow;

        /**
         * If the menu is open, the focus will move to the select control on window resize to avoid
         * mispositioning the menu when portaling
         */
        if (modalOverflow === 'hidden') return;

    }, [modalOverflow, withinModal]);

    const DropdownIndicator = props => {
        return (
            <components.DropdownIndicator {...props}>
                <Icon type="search"/>
            </components.DropdownIndicator>
        );
    };

    return <div className="Select">
        <Async
            loadOptions={loadOptions}
            options={cachedOptions}
            openMenuOnClick={false}
            styles={dropdownStyle}
            components={{DropdownIndicator}}
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
            menuPlacement={"auto"}
            defaultOptions
            menuPortalTarget={withinModal}
            onMenuOpen={(): void => setModalOverflow('hidden')}
            onMenuClose={(): void => setModalOverflow('auto')}
            {...rest}
        />

        <FieldErrors field={field} serverErrors={serverErrors}/>
    </div>;
}

export default Select;
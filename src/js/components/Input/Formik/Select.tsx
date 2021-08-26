import React, {FC, useEffect, useState} from 'react'

import {Dropdown, DropdownProps, dropdownStyle, Icon, Space, ValidationMessage} from "@castoredc/matter";
import {ErrorMessage, FieldInputProps, FieldProps} from "formik";
import {ActionMeta, OptionsType, ValueType} from "react-select/src/types";
import {components} from "react-select";
import Async, {AsyncProps} from 'react-select/async';

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
}

interface AsyncSelectProps extends SelectProps, AsyncProps<OptionType> {
    cachedOptions: OptionsType<OptionType>,
}

const Errors: FC<{ field: FieldInputProps<any>, serverErrors: any }> = ({field, serverErrors}) => {
    return <>
        <ErrorMessage
            name={field.name}
            render={msg => <>
                <Space bottom="default"/>
                <ValidationMessage type="error">{msg}</ValidationMessage>
            </>}
        />

        {serverErrors && <ValidationMessage type="error">
            {serverErrors.map((errorText, index) => (
                <div key={index}>
                    {errorText}
                </div>
            ))}
        </ValidationMessage>}
    </>;
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
                                     ...rest
                                 }) => {
    const touched = form.touched[field.name];
    const errors = form.errors[field.name];
    const serverErrors = serverError[field.name];

    return <>
        <Dropdown
            value={field.value}
            onChange={(value: ValueType<OptionType, IsMulti>, action: ActionMeta<OptionType>) => {
                if (onChange) {
                    onChange(value, action);
                }
                field.onChange({target: {name: field.name, value: value}});
            }}
            onBlur={field.onBlur}
            invalid={touched && !!errors}
            menuPlacement={menuPlacement ? menuPlacement : "auto"}
            {...rest}
        />

        <Errors field={field} serverErrors={serverErrors}/>
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

        <Errors field={field} serverErrors={serverErrors}/>
    </div>;
}

export default Select;
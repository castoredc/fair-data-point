import React, { FC, FormEvent, InputHTMLAttributes } from 'react';
import CustomIcon from 'components/Icon/CustomIcon';
import { FieldProps } from 'formik';
import FieldErrors from 'components/Input/Formik/Errors';
import _ from 'lodash';
import ListItemButton from '@mui/material/ListItemButton';
import ListItemIcon from '@mui/material/ListItemIcon';
import ListItemText from '@mui/material/ListItemText';

interface SelectableListItemsProps extends FieldProps {
    readOnly?: boolean;
    onChange?: (event: FormEvent<HTMLFieldSetElement>) => void;
    options: ListItemProps[];
    serverError?: any;
    multiple?: boolean;
}

interface ListItemProps extends InputHTMLAttributes<HTMLInputElement> {
    title: string;
    description?: string;
    value: string;
    icon?: React.ReactNode;
    customIcon?: string;
    fill?: boolean;
    active?: boolean;
    handleChange: () => void;
}

const SelectableListItems: FC<SelectableListItemsProps> = ({
                                                               field,
                                                               readOnly,
                                                               onChange,
                                                               options,
                                                               serverError,
                                                               multiple,
                                                           }) => {
    const serverErrors = serverError ? serverError[field.name] : undefined;

    return (
        <>
            <div>
                {options.map(option => {
                    return (
                        <SelectableListItem
                            key={option.value}
                            title={option.title}
                            description={option.description}
                            icon={option.icon}
                            customIcon={option.customIcon}
                            fill={option.fill}
                            active={multiple ? field.value.includes(option.value) : field.value === option.value}
                            value={option.value}
                            handleChange={() => {
                                const value = multiple ? _.xor(field.value, [option.value]) : option.value;
                                field.onChange({ target: { name: field.name, value } });
                            }}
                            role={multiple ? 'checkbox' : 'radio'}
                        />
                    );
                })}
            </div>

            <FieldErrors field={field} serverErrors={serverErrors} />
        </>
    );
};

const SelectableListItem: FC<ListItemProps> = ({
                                                   title,
                                                   description,
                                                   icon,
                                                   customIcon,
                                                   fill,
                                                   active,
                                                   handleChange,
                                                   role,
                                               }) => {
    return (
        <ListItemButton
            selected={active}
            onClick={handleChange}
        >
            {icon && <ListItemIcon>
                {icon}
            </ListItemIcon>}
            {customIcon && <ListItemIcon>
                <CustomIcon type={customIcon} />
            </ListItemIcon>}
            <ListItemText
                primary={title}
                secondary={description}
            />
        </ListItemButton>
    );
};

export default SelectableListItems;

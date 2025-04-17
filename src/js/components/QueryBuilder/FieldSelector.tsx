import { findOptionByValue } from '../../util';
import React, { FC } from 'react';
import { Field } from 'react-querybuilder';
import Select from '@mui/material/Select';
import MenuItem from '@mui/material/MenuItem';

interface FieldSelectorProps {
    options: Field[];
    value?: string;

    handleOnChange(value: any): void;
}

const FieldSelector: FC<FieldSelectorProps> = ({ options, value, handleOnChange }) => {
    const parsedOptions = options.map(field => {
        if (field.options) {
            return {
                value: field.name,
                label: field.label,
                options: field.options,
                name: field.name,
            };
        } else {
            return {
                value: field.name,
                label: field.label,
                name: field.name,
            };
        }
    });

    const selectedValue = findOptionByValue(value, parsedOptions);

    return <Select
        value={selectedValue}
        onChange={handleOnChange}
    >
        {parsedOptions.map((option: any) => {
            return <MenuItem value={option.value}>{option.label}</MenuItem>
        })}
    </Select>
};

export default FieldSelector;

import React, { FC } from 'react';
import { Field } from 'react-querybuilder';
import { NameLabelPair } from 'react-querybuilder/types/types';
import MenuItem from '@mui/material/MenuItem';
import Select from '@mui/material/Select';

interface OperatorSelectorProps {
    options: Field[];
    value?: string;

    handleOnChange(value: any): void;
}

const OperatorSelector: FC<OperatorSelectorProps> = ({ options, value, handleOnChange }) => {
    return (
        <Select
            value={value}
            onChange={handleOnChange}
        >
            {options.map((option: NameLabelPair) => {
                return <MenuItem value={option.name}>{option.label}</MenuItem>
            })}
        </Select>
    );
};

export default OperatorSelector;

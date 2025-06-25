import { NameLabelPair } from 'react-querybuilder/types/types';
import React, { FC } from 'react';
import Select from '@mui/material/Select';
import MenuItem from '@mui/material/MenuItem';

interface CombinatorSelectorProps {
    options: NameLabelPair[];
    value?: string;

    handleOnChange(event: any): void;
}

const CombinatorSelector: FC<CombinatorSelectorProps> = ({ options, value, handleOnChange }) => {
    return (
        <Select
            value={value}
            onChange={handleOnChange}
        >
            {options.map((option: NameLabelPair) => {
                return <MenuItem key={option.name} value={option.name}>{option.label}</MenuItem>;
            })}
        </Select>
    );
};

export default CombinatorSelector;

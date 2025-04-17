import React, { FC } from 'react';
import { Field } from 'react-querybuilder';
import Select from '@mui/material/Select';
import MenuItem from '@mui/material/MenuItem';
import { ListSubheader } from '@mui/material';

interface FieldSelectorProps {
    options: Field[];
    value?: string;

    handleOnChange(event: any): void;
}

const FieldSelector: FC<FieldSelectorProps> = ({ options, value, handleOnChange }) => {
    return <Select
        value={value}
        onChange={handleOnChange}
        sx={{ width: '160px' }}
        defaultValue=""
    >
        {options.map(field => {
            if (field.options) {
                return [
                    <ListSubheader>{field.label}</ListSubheader>,
                    ...field.options.map((option: any) => (
                        <MenuItem key={option.value} value={option.value}>{option.label}</MenuItem>
                    )),
                ];
            } else {
                return <MenuItem key={field.name} value={field.name}>{field.label}</MenuItem>;
            }
        })}
    </Select>;
};

export default FieldSelector;

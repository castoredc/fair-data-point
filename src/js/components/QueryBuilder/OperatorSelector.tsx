import { findOptionByValue } from '../../util';
import { Dropdown } from '@castoredc/matter';
import React, { FC } from 'react';
import { Field } from 'react-querybuilder';

interface OperatorSelectorProps {
    options: Field[];
    value?: string;
    handleOnChange(value: any): void;
}

const OperatorSelector: FC<OperatorSelectorProps> = ({ options, value, handleOnChange }) => {
    const parsedOptions = options.map(option => ({
        value: option.name,
        label: option.label,
        name: option.name,
    }));

    const selectedValue = findOptionByValue(value, parsedOptions);

    return (
        <Dropdown
            value={
                value
                    ? {
                          value: selectedValue.name,
                          label: selectedValue.label,
                      }
                    : null
            }
            onChange={e => handleOnChange(e ? e.value : '')}
            menuPosition="fixed"
            width="minimum"
            options={parsedOptions}
        />
    );
};

export default OperatorSelector;

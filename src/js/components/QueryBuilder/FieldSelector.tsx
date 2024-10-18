import { NameLabelPair } from 'react-querybuilder/types/types';
import { findOptionByValue } from '../../util';
import { Dropdown } from '@castoredc/matter';
import React, { FC } from 'react';
import { Field } from 'react-querybuilder';

interface FieldSelectorProps {
    options: Field[],
    value?: string;
    handleOnChange(value: any): void;
}

const FieldSelector: FC<FieldSelectorProps> = ({ options, value, handleOnChange }) => {
    const parsedOptions = options.map((field) => {
        if(field.options) {
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

    return <Dropdown
        value={selectedValue}
        onChange={(e) => handleOnChange(e.value)}
        menuPosition="fixed"
        width="tiny"
        options={parsedOptions}
    />
};

export default FieldSelector;
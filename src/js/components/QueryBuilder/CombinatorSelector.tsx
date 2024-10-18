import { NameLabelPair } from 'react-querybuilder/types/types';
import { findOptionByValue } from '../../util';
import { Dropdown } from '@castoredc/matter';
import React, { FC } from 'react';

interface CombinatorSelectorProps {
    options: NameLabelPair[],
    value?: string;
    handleOnChange(value: any): void;
}

const CombinatorSelector: FC<CombinatorSelectorProps> = ({ options, value, handleOnChange }) => {
    const parsedOptions = options.map((option: NameLabelPair) => ({
        value: option.name,
        label: option.label,
        name: option.name,
    }));

    const selectedValue = findOptionByValue(value, parsedOptions);

    return <Dropdown
        value={value ? {
            value: selectedValue.name,
            label: selectedValue.label
        }: null}
        onChange={(e) => handleOnChange(e ? e.value : '')}
        menuPosition="fixed"
        width="minimum"
        options={parsedOptions}
    />
};

export default CombinatorSelector;
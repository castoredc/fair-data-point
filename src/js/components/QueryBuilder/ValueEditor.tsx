import React from 'react';
import { ValueEditorProps as QueryBuilderValueEditorProps } from 'react-querybuilder/types/types';
import { PrefixType } from 'types/PrefixType';
import { InstituteType } from 'types/InstituteType';
import { Dropdown, TextInput } from '@castoredc/matter';
import MaskedInput from 'react-text-mask';
import { findOptionByValue } from '../../util';

interface ValueEditorProps extends QueryBuilderValueEditorProps {
    prefixes: PrefixType[],
    institutes: InstituteType[],
}

export const ValueEditor: React.FC<ValueEditorProps> = ({ field, fieldData, operator, handleOnChange, value, values, prefixes, institutes }) => {
    const handleUrlChange = (value, prefixes) => {
        let newValue = value;
        const regex = /^([^:]*):(.*)/;
        const matches = regex.exec(newValue);

        if (matches !== null) {
            const matchedPrefix = matches[1];
            const foundPrefix = prefixes.find(prefix => {
                return prefix.prefix === matchedPrefix;
            });

            if (typeof foundPrefix !== 'undefined') {
                newValue = foundPrefix.uri + matches[2];
            }
        }

        handleOnChange(newValue);
    };

    if (operator === 'null' || operator === 'notNull') {
        return null;
    }

    if (typeof field !== 'undefined') {
        if (fieldData.valueType === 'institute') {
            const parsedInstitutes = institutes.map(institute => {
                return { value: institute.id, label: institute.name };
            });
            const selectedValue = findOptionByValue(value, parsedInstitutes);

            return (
                <Dropdown
                    value={selectedValue}
                    onChange={e => handleOnChange(e.value)}
                    menuPosition="fixed"
                    width="tiny"
                    options={parsedInstitutes}
                />
            );
        }

        if (fieldData.valueType === 'annotated') {
            return <TextInput className="ValueEditor" value={value} onChange={e => handleUrlChange(e.target.value, prefixes)} inputSize="20rem" />;
        }

        if (fieldData.dataType === 'date' || fieldData.dataType === 'dateTime' || fieldData.dataType === 'time') {
            let mask: any[] = [];
            let placeholder = '';

            if (fieldData.dataType === 'date') {
                mask = [/\d/, /\d/, '-', /\d/, /\d/, '-', /\d/, /\d/, /\d/, /\d/];
                placeholder = 'DD-MM-YYYY';
            } else if (fieldData.dataType === 'dateTime') {
                mask = [/\d/, /\d/, '-', /\d/, /\d/, '-', /\d/, /\d/, /\d/, /\d/, ' ', /\d/, /\d/, ':', /\d/, /\d/];
                placeholder = 'DD-MM-YYYY hh:mm';
            } else if (fieldData.dataType === 'time') {
                mask = [/\d/, /\d/, ':', /\d/, /\d/];
                placeholder = 'hh:mm';
            }

            return (
                <MaskedInput
                    mask={mask}
                    className="ValueEditor"
                    ref={r => {
                        // @ts-ignore
                        this.input = r;
                    }}
                    value={value}
                    onChange={e => handleOnChange(e.target.value)}
                    render={(ref, props) => <TextInput forwardRef={ref} {...props} inputSize="20rem" placeholder={placeholder} />}
                />
            );
        }
    }

    return <TextInput className="ValueEditor" value={value} onChange={e => handleOnChange(e.target.value)} inputSize="20rem" />;
};

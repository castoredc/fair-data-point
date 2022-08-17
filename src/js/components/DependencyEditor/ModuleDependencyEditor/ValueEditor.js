import React from 'react';
import { TextInput } from '@castoredc/matter';
import MaskedInput from 'react-text-mask';

export const ValueEditor = ({ field, fieldData, operator, handleOnChange, value, values, prefixes }) => {
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

    if (fieldData.valueType === 'annotated') {
        return <TextInput className="ValueEditor" value={value} onChange={e => handleUrlChange(e.target.value, prefixes)} inputSize="20rem" />;
    }

    if (fieldData.dataType === 'date' || fieldData.dataType === 'dateTime' || fieldData.dataType === 'time') {
        let mask = [];
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
                    this.input = r;
                }}
                value={value}
                onChange={e => handleOnChange(e.target.value)}
                render={(ref, props) => <TextInput forwardRef={ref} {...props} inputSize="20rem" placeholder={placeholder} />}
            />
        );
    }

    return <TextInput className="ValueEditor" value={value} onChange={e => handleOnChange(e.target.value)} inputSize="20rem" />;
};

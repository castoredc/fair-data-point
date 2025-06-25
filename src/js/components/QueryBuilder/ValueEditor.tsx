import React from 'react';
import { ValueEditorProps as QueryBuilderValueEditorProps } from 'react-querybuilder/types/types';
import { PrefixType } from 'types/PrefixType';
import { InstituteType } from 'types/InstituteType';
import MaskedInput from 'react-text-mask';
import Select from '@mui/material/Select';
import MenuItem from '@mui/material/MenuItem';
import { TextField } from '@mui/material';

interface ValueEditorProps extends QueryBuilderValueEditorProps {
    prefixes: PrefixType[];
    institutes: InstituteType[];
}

export const ValueEditor: React.FC<ValueEditorProps> = ({
                                                            field,
                                                            fieldData,
                                                            operator,
                                                            handleOnChange,
                                                            value,
                                                            values,
                                                            prefixes,
                                                            institutes,
                                                        }) => {
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
            return (
                <Select
                    value={value}
                    onChange={e => handleOnChange(e.target.value)}
                    fullWidth
                >
                    {institutes.map(institute => {
                        return <MenuItem key={institute.id} value={institute.id}>{institute.name}</MenuItem>;
                    })}
                </Select>
            );
        }

        if (fieldData.valueType === 'annotated') {
            return <TextField
                className="ValueEditor"
                value={value}
                onChange={e => handleUrlChange(e.target.value, prefixes)}
                fullWidth
            />;
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
                    render={(ref, props) => <TextField forwardRef={ref} {...props} width="20rem"
                                                       placeholder={placeholder} />}
                />
            );
        }
    }

    return <TextField
        className="ValueEditor"
        value={value}
        onChange={e => handleOnChange(e.target.value)}
        fullWidth
    />;
};

import React from "react";
import Input from "../../Input";

export const ValueEditor = ({
                                field,
                                fieldData,
                                operator,
                                handleOnChange,
                                value,
                                values,
                                prefixes,
                            }) => {

    const required = "This field is required";
    const validUrl = "Please enter a valid URI";

    const handleUrlChange = (value, prefixes) => {
        let newValue = value;
        const regex = /^([^:]*):(.*)/;
        const matches = regex.exec(newValue);

        if (matches !== null) {
            const matchedPrefix = matches[1];
            const foundPrefix = prefixes.find((prefix) => {
                return prefix.prefix === matchedPrefix
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
        return <Input className="ValueEditor" value={value}
                      validators={['required', 'isUrl']}
                      errorMessages={[required, validUrl]}
                      onChange={(e) => handleUrlChange(e.target.value, prefixes)}
                      width="20rem"
        />;
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

        return <Input className="ValueEditor"
                      value={value}
                      validators={['required']}
                      errorMessages={[required]}
                      onChange={(e) => handleOnChange(e.target.value)}
                      mask={mask}
                      placeholder={placeholder}
                      width="20rem"
        />;
    }

    return <Input className="ValueEditor" value={value}
                  validators={['required']}
                  errorMessages={[required]}
                  onChange={(e) => handleOnChange(e.target.value)}
                  width="20rem"
    />;
};
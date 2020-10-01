import React from "react";
import Input from "../../Input";
import Dropdown from "../../Input/Dropdown";

export const ValueEditor = ({
                                field,
                                fieldData,
                                operator,
                                handleOnChange,
                                value,
                                values,
                                prefixes,
                                institutes
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

    if (typeof field !== 'undefined') {
        if (field.valueType === 'institute') {
            return <Dropdown value={value}
                             onChange={(e) => handleOnChange(e.value)}
                             menuPosition="fixed"
                             width="tiny"
                             options={institutes.map((institute) => {
                                 return {value: institute.id, label: institute.name};
                             })}
            />;
        }

        if (field.valueType === 'annotated') {
            return <Input className="ValueEditor" value={value}
                          validators={['required', 'isUrl']}
                          errorMessages={[required, validUrl]}
                          onChange={(e) => handleUrlChange(e.target.value, prefixes)}
                          width="20rem"
            />;
        }

        if (field.dataType === 'date' || field.dataType === 'dateTime' || field.dataType === 'time') {
            let mask = [];
            let placeholder = '';

            if (field.dataType === 'date') {
                mask = [/\d/, /\d/, '-', /\d/, /\d/, '-', /\d/, /\d/, /\d/, /\d/];
                placeholder = 'DD-MM-YYYY';
            } else if (field.dataType === 'dateTime') {
                mask = [/\d/, /\d/, '-', /\d/, /\d/, '-', /\d/, /\d/, /\d/, /\d/, ' ', /\d/, /\d/, ':', /\d/, /\d/];
                placeholder = 'DD-MM-YYYY hh:mm';
            } else if (field.dataType === 'time') {
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
    }

    return <Input className="ValueEditor" value={value}
                  validators={['required']}
                  errorMessages={[required]}
                  onChange={(e) => handleOnChange(e.target.value)}
                  width="20rem"
    />;
};
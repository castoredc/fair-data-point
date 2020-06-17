import React from 'react';
import {ValidatorComponent} from 'react-form-validator-core';
import Form from 'react-bootstrap/Form'
import AsyncSelect from 'react-select/async';
import {ChoiceOption, Dropdown as CastorDropdown, TextStyle} from '@castoredc/matter';

import {components} from 'react-select';

import './Input.scss'

class Dropdown extends ValidatorComponent {
    constructor(props) {
        super(props);

        this.state = {
            ...this.state,
            cachedOptions: []
        };
    }

    loadOptions = (inputValue, callback) => {
        const { loadOptions } = this.props;

        if (this.timeout) {
            clearTimeout(this.timeout);
            this.timeout = null
        }

        this.timeout = setTimeout(() => {
            loadOptions(inputValue, (options) => {
                this.setState({
                    cachedOptions: options
                });

                callback(options);
            });
        }, 1000);
    };

    render() {
        const {   serverError,
                  placeholder,
                  isDisabled,
                  options,
                  value,
                  width,
                  async = false,
                  onChange,
                  isMulti = false,
              } = this.props;
        const { cachedOptions, isValid } = this.state;

        let SelectComponent = null;

        if(async) {
            SelectComponent = <AsyncSelect
                loadOptions={this.loadOptions}
                options={cachedOptions}
                openMenuOnClick={false}
                ref={(r) => { this.input = r; }}
                menuPosition="fixed"
                menuPlacement="auto"
                onChange={onChange}
            />
        } else if(isMulti) {
            const CustomOption = props => (
                <components.Option className="DropdownMultiOption" {...props}>
                    <ChoiceOption
                        labelText={props.data.label}
                        checked={props.isSelected}
                        onChange={() => undefined}
                    />
                </components.Option>
            );

            const MultiValue = ({ children, ...props }) => {
                const value = props.getValue();

                if (value.length === 1) {
                    return  <div>{value[0].label}</div>;
                }

                if (value.length === options.length) {
                    return props.index === 0 ? <div>All</div> : null;
                }

                return props.index === 0 ? <div>{value.length} selected</div> : null;
            };

            SelectComponent = <CastorDropdown
                invalid={!isValid}
                onChange={onChange}
                value={value}
                options={options}
                placeholder={placeholder}
                isDisabled={isDisabled}
                width={width}
                isMulti={true}
                closeMenuOnSelect={false}
                hideSelectedOptions={false}
                isClearable={false}
                menuPosition="fixed"
                menuPlacement="auto"
                components={{
                    Option: CustomOption,
                    MultiValue,
                }}
            />;
        } else {
            SelectComponent = <CastorDropdown
                invalid={!isValid}
                onChange={onChange}
                value={value}
                options={options}
                placeholder={placeholder}
                isDisabled={isDisabled}
                width={width}
                menuPosition="fixed"
                menuPlacement="auto"
            />;
        }

        return (
            <Form.Group className="Select" onClick={this.props.onClick}
                        ref={(r) => { this.input = r; }}>
                {SelectComponent}
                {this.errorText()}
                {serverError && serverError.map((errorText, index) => (
                    <TextStyle key={index} variation="error">
                        {errorText}
                    </TextStyle>
                ))}
            </Form.Group>
        );
    }

    errorText() {
        const { isValid } = this.state;

        if (isValid) {
            return null;
        }

        return (
            <TextStyle variation="error">
                {this.getErrorMessage()}
            </TextStyle>
        );
    }
}

export default Dropdown;

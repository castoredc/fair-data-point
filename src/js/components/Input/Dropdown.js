import React from 'react';
import {ValidatorComponent} from 'react-form-validator-core';
import Form from 'react-bootstrap/Form'
import AsyncSelect from 'react-select/async';
import {Dropdown as CastorDropdown, TextStyle} from '@castoredc/matter';

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
                  onChange } = this.props;
        const { cachedOptions, isValid } = this.state;

        return (
            <Form.Group className="Select" onClick={this.props.onClick}
                        ref={(r) => { this.input = r; }}>
                {async ? <AsyncSelect
                    loadOptions={this.loadOptions}
                    options={cachedOptions}
                    openMenuOnClick={false}
                    ref={(r) => { this.input = r; }}
                /> : <CastorDropdown
                    invalid={!isValid}
                    onChange={onChange}
                    value={value}
                    options={options}
                    placeholder={placeholder}
                    isDisabled={isDisabled}
                    width={width}
                    menuPlacement="auto"
                /> }
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

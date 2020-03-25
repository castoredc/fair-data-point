import React from 'react';
import { ValidatorComponent } from 'react-form-validator-core';
import Form from 'react-bootstrap/Form'
import Select from 'react-select'

import './Input.scss'

class Dropdown extends ValidatorComponent {

    render() {
        const { errorMessages, serverError, validators, requiredError, validatorListener, type, mask, ...rest } = this.props;

        return (
            <Form.Group className="Select" onClick={this.props.onClick}>
                <Select
                    {...rest}
                    ref={(r) => { this.input = r; }}
                />
                {this.errorText()}
                {serverError && serverError.map((errorText, index) => (
                    <Form.Text key={index} className="InputError">
                        {errorText}
                    </Form.Text>
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
            <Form.Text className="InputError">
                {this.getErrorMessage()}
            </Form.Text>
        );
    }
}

export default Dropdown;

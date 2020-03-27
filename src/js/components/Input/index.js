import React from 'react';
import {ValidatorComponent} from 'react-form-validator-core';
import Form from 'react-bootstrap/Form'
import MaskedInput from 'react-text-mask'

import './Input.scss'

class Input extends ValidatorComponent {

    render() {
        const { errorMessages, serverError, validators, requiredError, validatorListener, mask, ...rest } = this.props;
        return (
            <Form.Group className="Input">
                {mask ? <MaskedInput
                        mask={mask}
                        className="form-control"
                        {...rest}
                        ref={(r) => { this.input = r; }}
                    /> :
                    <Form.Control
                        {...rest}
                        ref={(r) => { this.input = r; }}
                    />
                }
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

export default Input;

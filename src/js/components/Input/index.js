import React from 'react';
import {ValidatorComponent} from 'react-form-validator-core';
import Form from 'react-bootstrap/Form'
import MaskedInput from 'react-text-mask'

import './Input.scss'
import {TextInput, TextStyle} from "@castoredc/matter";

class Input extends ValidatorComponent {
    render() {
        const { errorMessages, serverError, validators, requiredError, validatorListener, mask, ...rest } = this.props;
        const { isValid } = this.state;
        return (
            <div className="Input">
                {mask ? <MaskedInput
                        mask={mask}
                        className="form-control"
                        {...rest}
                        ref={(r) => { this.input = r; }}
                    /> : <TextInput
                        {...rest}
                        invalid={!isValid}
                        ref={(r) => { this.input = r; }}
                    />
                }
                {this.errorText()}
                {serverError && serverError.map((errorText, index) => (
                    <TextStyle key={index} variation="error">
                        {errorText}
                    </TextStyle>
                ))}
            </div>
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

export default Input;

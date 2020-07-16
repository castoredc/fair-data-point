import React from 'react';
import Form from "react-bootstrap/Form";
import {ValidatorComponent} from "react-form-validator-core";
import {ChoiceOption, TextStyle} from "@castoredc/matter";
import './Input.scss'

export default class RadioGroup extends ValidatorComponent {
    render() {
        const { value, options, onChange, name, variant, readOnly, serverError } = this.props;

        return <Form.Group className="Input">
            <div
                className={'RadioGroup'  + (variant === 'horizontal' ? ' Horizontal' : ' Vertical')}
                ref={(r) => { this.input = r; }}
            >
                {options.map((option) => {
                    return <ChoiceOption
                        key={option.value}
                        labelText={option.label}
                        type="radio"
                        name={name}
                        onChange={readOnly ? null : () => {onChange({target: { name: name, value: option.value}})}}
                        value={option.value}
                        checked={value === option.value}
                        readOnly={readOnly}
                    />
                })}
                {this.errorText()}

                {serverError && serverError.map((errorText, index) => (
                    <TextStyle key={index} variation="error">
                        {errorText}
                    </TextStyle>
                ))}
            </div>
        </Form.Group>;
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
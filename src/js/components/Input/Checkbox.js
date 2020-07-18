import React from 'react';
import {ValidatorComponent} from "react-form-validator-core";
import Form from 'react-bootstrap/Form'
import './Input.scss'
import {ChoiceOption} from "@castoredc/matter";

export default class Checkbox extends ValidatorComponent {
    render() {
        const {
            label,
            value = false,
            onChange,
            name,
        } = this.props;

        return (
            <div className="CheckboxFormGroup">
                <ChoiceOption
                    labelText={label}
                    type="checkbox"
                    onChange={() => onChange({target: {name: name, value: !value}})}
                    checked={value}
                />
                {this.errorText()}
            </div>
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

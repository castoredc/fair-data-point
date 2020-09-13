import React from 'react';
import {ValidatorComponent} from "react-form-validator-core";
import './Input.scss'
import {ChoiceOption, TextStyle} from "@castoredc/matter";

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
            <TextStyle variation="error">
                {this.getErrorMessage()}
            </TextStyle>
        );
    }
}

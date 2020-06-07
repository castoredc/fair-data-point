import React from 'react';
import Form from "react-bootstrap/Form";
import {ValidatorComponent} from "react-form-validator-core";
import {Choice, TextStyle} from "@castoredc/matter";

export default class RadioGroup extends ValidatorComponent {
    render() {
        const { value, options, onChange, name, variant, isValid } = this.props;
        return <Form.Group className="Input">
            <div
                className={'RadioGroup'  + (variant === 'horizontal' ? ' Horizontal' : ' Vertical')}
                ref={(r) => { this.input = r; }}
            >
                <Choice
                    collapse={variant === 'horizontal'}
                    labelText={name}
                    hideLabel
                    name={name}
                    onChange={onChange}
                    options={options.map((option) => {
                        return {labelText: option.label, value: option.value};
                    })}
                    value={value}
                />
                {this.errorText()}
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
import React from 'react';
import {classNames} from '../../../util';
import {ValidatorComponent} from "react-form-validator-core";
import Form from 'react-bootstrap/Form'

import './Checkbox.scss';
import './Input.scss'

class Checkbox extends ValidatorComponent {

    render() {
        const {
            errorMessages, validators, requiredError, validatorListener, type,
            invalid,
            label,
            value = false,
            onChange,
            required,
            name,
            ...rest
        } = this.props;

        return (
            <Form.Group className="CheckboxGroup">
                <label className={classNames('Checkbox', invalid && 'error')}>
                    <input
                        type="checkbox"
                        checked={value}
                        required={required}
                        name={name}
                        onChange={() => onChange({target: {name: name, value: !value}})}
                        ref={(r) => { this.input = r; }}
                        {...rest}
                    />
                    <div className="icon"/>
                    <span dangerouslySetInnerHTML={{ __html: label }}></span>
                </label>
                {this.errorText()}
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

export const CheckboxGroup = ({ label, checkboxes }) => {
  return (
    <div className="CheckboxGroup">
      {checkboxes.map((checkbox, index) => (
        <Checkbox key={index} {...checkbox} />
      ))}
    </div>
  );
};

export default Checkbox;

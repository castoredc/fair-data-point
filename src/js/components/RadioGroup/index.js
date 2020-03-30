import React from 'react';
import './RadioGroup.scss';
import Form from "react-bootstrap/Form";

const RadioGroup = ({ label, value, options, onChange, name, variant }) => (
    <Form.Group className="Input">
          <div className={'RadioGroup'  + (variant === 'horizontal' ? ' Horizontal' : ' Vertical')}>
            {options.map((option, index) => (
              <RadioOption
                key={index}
                label={option.label}
                checked={value === option.value}
                value={option.value}
                onChange={() => onChange({target: {name: name, value: option.value}})}
                name={name}
              />
            ))}
          </div>
    </Form.Group>
);

const RadioOption = ({ label, checked, onChange, name, value }) => (
  <label className="RadioOption">
    <input type="radio" onChange={onChange} value={value}
           checked={checked}
           name={name} />
    <div className="icon" />
      <span>{label}</span>
  </label>
);

export default RadioGroup;

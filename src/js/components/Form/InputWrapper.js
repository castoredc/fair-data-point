import React from 'react';
import FieldLabel from '../FieldLabel';
import './InputWrapper.scss';

const InputWrapper = ({
  children,
  id,
  label,
  helpText,
  optional,
  labelAction,
}) => (
  <div className="InputWrapper">
    <div className="Label">
      {label && <FieldLabel id={id}>{label}</FieldLabel>}
      {optional && <span className="InformationLabel">(Optional)</span>}
    </div>
    {children}
    {helpText && <p className="HelpText">{helpText}</p>}
    {labelAction}
  </div>
);

export default InputWrapper;

export const withWrapper = Component => ({
  helpText,
  label,
  optional,
  labelAction,
  id,
  ...rest
}) => (
  <InputWrapper
    id={id}
    helpText={helpText}
    label={label}
    optional={optional}
    labelAction={labelAction}
  >
    <Component id={id} {...rest} />
  </InputWrapper>
);

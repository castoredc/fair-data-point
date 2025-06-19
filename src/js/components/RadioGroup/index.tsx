import React, { FC } from 'react';
import FormControlLabel from '@mui/material/FormControlLabel';
import Checkbox from '@mui/material/Checkbox';
import FormGroup from '@mui/material/FormGroup';
import { Radio, RadioGroup as MuiRadioGroup } from '@mui/material';
import Typography from '@mui/material/Typography';

export type OptionValue = string | number;

export type Option = {
    label: string,
    value: OptionValue,
}

interface RadioGroupProps {
    readOnly?: boolean;
    onChange?: (event: React.ChangeEvent<HTMLInputElement>) => void;
    options: Option[];
    collapse?: boolean;
    multiple?: boolean;
    value?: OptionValue | OptionValue[] | null | undefined;
    name: string,
    disabled?: boolean;
}

const RadioGroup: FC<RadioGroupProps> = ({ options, value, multiple, onChange, disabled, name }) => {
    const parsedValue = value && Array.isArray(value) ? value : [];

    if (multiple) {
        return <FormGroup>
            {options.map((option) => {
                const checked = parsedValue.includes(option.value);

                return <FormControlLabel
                    key={option.value}
                    control={
                        <Checkbox checked={checked} onChange={onChange} name={option.value as string} />
                    }
                    label={option.label}
                />;
            })}
        </FormGroup>;
    }

    return <MuiRadioGroup
        onChange={onChange}
        value={value}
        name={name}
    >
        {options.map((option) => {
            return <FormControlLabel
                key={option.value}
                control={
                    <Radio
                        sx={{
                        '& .MuiSvgIcon-root': {
                            fontSize: 18,
                        },
                    }} />
                }
                label={<Typography sx={{
                    fontSize: '0.875rem',
                }}>{option.label}</Typography>}
                value={option.value}
                disabled={disabled}
            />;
        })}
    </MuiRadioGroup>;
};

export default RadioGroup;
import React, { FC } from 'react';
import { Typography } from '@mui/material';

interface FormHeadingProps {
    label: string;
}

const FormHeading: FC<FormHeadingProps> = ({ label }) => {
    return (
        <div className="FormHeading">
            <Typography variant="h4">{label}</Typography>
        </div>
    );
};

export default FormHeading;

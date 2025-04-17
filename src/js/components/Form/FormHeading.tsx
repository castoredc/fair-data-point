import React, { FC } from 'react';
import { Box, Typography } from '@mui/material';

interface FormHeadingProps {
    label: string;
}

const FormHeading: FC<FormHeadingProps> = ({ label }) => {
    return (
        <Box 
            sx={{ 
                mb: 4,
                pb: 2,
                borderBottom: 1,
                borderColor: 'divider'
            }}
        >
            <Typography 
                variant="h4" 
                sx={{ 
                    fontWeight: 500,
                    color: 'text.primary'
                }}
            >
                {label}
            </Typography>
        </Box>
    );
};

export default FormHeading;

import React, { FC } from 'react';
import { Box, Typography, Paper } from '@mui/material';

type FormGroupProps = {
    label: string;
    children: React.ReactNode;
};

const FormGroup: FC<FormGroupProps> = ({ label, children }) => {
    return (
        <Box sx={{ mb: 4 }}>
            <Box sx={{ mb: 2 }}>
                <Typography variant="h5" component="h2" sx={{ fontWeight: 500 }}>
                    {label}
                </Typography>
            </Box>
            <Paper 
                elevation={0} 
                sx={{ 
                    p: 3,
                    bgcolor: 'background.paper',
                    borderRadius: 2
                }}
            >
                {children}
            </Paper>
        </Box>
    );
};

export default FormGroup;

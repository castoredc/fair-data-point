import React from 'react';
import { Typography, Box } from '@mui/material';

interface NoResultsProps {
    children: React.ReactNode;
}

const NoResults: React.FC<NoResultsProps> = ({ children }) => {
    return (
        <Box sx={{ 
            textAlign: 'center',
            py: 6,
            width: '100%',
        }}>
            <Typography variant="body1" color="text.secondary">
                {children}
            </Typography>
        </Box>
    );
};

export default NoResults;

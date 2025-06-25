import React from 'react';
import { Stack, StackProps } from '@mui/material';

interface PageButtonsProps extends StackProps {
    children: React.ReactNode;
}

const PageButtons: React.FC<PageButtonsProps> = ({ children, ...props }) => {
    return (
        <Stack
            direction="row"
            spacing={2}
            sx={{
                justifyContent: 'flex-end',
                mb: 2,
                ...props.sx,
            }}
            {...props}
        >
            {children}
        </Stack>
    );
};

export default PageButtons;

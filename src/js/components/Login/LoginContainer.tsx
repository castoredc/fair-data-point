import React from 'react';
import { Box, BoxProps, Typography, useTheme } from '@mui/material';

interface LoginContainerProps extends BoxProps {
    children: React.ReactNode;
    title?: string;
    logo?: React.ReactNode;
}

const LoginContainer: React.FC<LoginContainerProps> = ({ children, title, logo, ...props }) => {
    const theme = useTheme();

    return (
        <Box
            sx={{
                minHeight: '100vh',
                display: 'flex',
                alignItems: 'center',
                justifyContent: 'center',
                flexDirection: 'column',
                p: 5,
                [theme.breakpoints.down('sm')]: {
                    p: 2,
                },
            }}
        >
            <Box
                sx={{
                    maxWidth: 550,
                    width: '100%',
                    m: 'auto',
                    display: 'flex',
                    flexDirection: 'column',
                    alignItems: 'center',
                    gap: 4,
                    ...props.sx,
                }}
                {...props}
            >
                {logo && (
                    <Box
                        sx={{
                            textAlign: 'center',
                            '& .Logo': {
                                height: 32,
                                width: 'auto',
                                '& path': {
                                    fill: theme.palette.primary.main,
                                },
                            },
                        }}
                    >
                        {logo}
                    </Box>
                )}
                {title && (
                    <Typography
                        variant="h4"
                        component="h1"
                        sx={{
                            textAlign: 'center',
                            color: theme.palette.text.primary,
                            mb: 2,
                        }}
                    >
                        {title}
                    </Typography>
                )}
                {children}
            </Box>
        </Box>
    );
};

export default LoginContainer;

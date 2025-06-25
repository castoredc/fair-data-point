import React from 'react';
import { Box, Typography, useTheme } from '@mui/material';

interface WizardLayoutProps {
    children: React.ReactNode;
}

interface WizardHeaderProps {
    title: React.ReactNode;
    description?: React.ReactNode;
}

export const WizardHeader: React.FC<WizardHeaderProps> = ({ title, description }) => {
    const theme = useTheme();

    return (
        <Box
            component="header"
            sx={{
                my: 6,
                pb: 6,
                borderBottom: `1px solid ${theme.palette.divider}`,
            }}
        >
            <Typography
                variant="h3"
                component="h1"
                gutterBottom={!!description}
                sx={{ fontSize: 32 }}
            >
                {title}
            </Typography>
            {description && (
                <Typography
                    variant="subtitle1"
                    sx={{
                        fontWeight: 600,
                        lineHeight: 1.31,
                        color: theme.palette.text.primary,
                    }}
                >
                    {description}
                </Typography>
            )}
        </Box>
    );
};

export const WizardBrand: React.FC<{ logo: React.ReactNode; text: string }> = ({
                                                                                   logo,
                                                                                   text,
                                                                               }) => {
    const theme = useTheme();

    return (
        <Box
            sx={{
                mb: 5,
                display: 'flex',
                alignItems: 'center',
                '& .Logo': {
                    height: 32,
                    width: 'auto',
                    '& path': {
                        fill: theme.palette.primary.main,
                    },
                },
            }}
        >
            <Box>{logo}</Box>
            <Typography
                variant="subtitle1"
                sx={{
                    fontWeight: 600,
                    color: theme.palette.text.primary,
                    ml: 3,
                    pl: 3,
                    borderLeft: `1px solid ${theme.palette.divider}`,
                    lineHeight: '32px',
                }}
            >
                {text}
            </Typography>
        </Box>
    );
};

const WizardLayout: React.FC<WizardLayoutProps> = ({ children }) => {
    return (
        <Box
            sx={{
                pt: 6,
                pb: 3,
                px: 3,
                maxWidth: 980,
                mx: 'auto',
            }}
        >
            {children}
        </Box>
    );
};

export default WizardLayout;

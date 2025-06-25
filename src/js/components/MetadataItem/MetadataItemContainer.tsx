import React from 'react';
import { Box, Typography } from '@mui/material';

interface MetadataItemContainerProps {
    label: string;
    className?: string;
    table?: boolean;
    children: React.ReactNode;
    hideLabel?: boolean;
}

const MetadataItemContainer: React.FC<MetadataItemContainerProps> = ({
                                                                         label,
                                                                         className,
                                                                         table,
                                                                         children,
                                                                         hideLabel = false,
                                                                     }) => {
    return (
        <Box
            sx={{
                mb: 2,
                ...(table && {
                    display: 'table-row',
                    '& > *': {
                        display: 'table-cell',
                        py: 1,
                        px: 2,
                        borderBottom: 1,
                        borderColor: 'divider',
                    },
                }),
            }}
        >
            {!hideLabel && (
                <Typography
                    component="div"
                    sx={{
                        fontSize: '0.875rem',
                        fontWeight: 600,
                        lineHeight: 1.43,
                        color: 'text.primary',
                        mb: 0.5,
                    }}
                >
                    {label}
                </Typography>
            )}
            <Typography
                component="div"
                sx={{
                    fontSize: '1rem',
                    lineHeight: 1.5,
                    color: 'text.secondary',
                }}
            >
                {children}
            </Typography>
        </Box>
    );
};

export default MetadataItemContainer;

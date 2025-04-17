import React, { FC } from 'react';
import { Box, FormLabel, Stack, Tooltip, Typography } from '@mui/material';
import InfoIcon from '@mui/icons-material/Info';

interface FormItemProps {
    label?: string;
    children: React.ReactNode;
    hidden?: boolean;
    inline?: boolean;
    align?: 'left' | 'center' | 'right';
    className?: string;
    tooltip?: string;
    details?: string;
    isRequired?: boolean;
}

const FormItem: FC<FormItemProps> = ({
    label,
    children,
    hidden,
    inline,
    align = 'left',
    tooltip,
    details,
    isRequired,
}) => {
    if (hidden) {
        return null;
    }

    return (
        <Box 
            sx={{
                mb: 2,
                ...(inline && {
                    display: 'flex',
                    alignItems: 'center',
                    '& > *:first-of-type': {
                        mr: 2,
                        minWidth: '200px'
                    }
                })
            }}
        >
            {label && (
                <Box sx={{ mb: 1 }}>
                    <Stack 
                        direction="row" 
                        spacing={1} 
                        alignItems="center"
                        sx={{
                            textAlign: align,
                            width: '100%'
                        }}
                    >
                        <FormLabel 
                            component="label"
                            sx={{
                                color: 'text.primary',
                                fontSize: '0.875rem',
                                fontWeight: 500,
                                lineHeight: 1.5
                            }}
                        >
                            {label}
                            {isRequired && (
                                <Tooltip title="This field is required">
                                    <Typography 
                                        component="span" 
                                        sx={{ 
                                            color: 'error.main',
                                            ml: 0.5
                                        }}
                                    >
                                        *
                                    </Typography>
                                </Tooltip>
                            )}
                        </FormLabel>
                        {tooltip && (
                            <Tooltip title={tooltip}>
                                <InfoIcon 
                                    sx={{ 
                                        fontSize: '1rem',
                                        color: 'action.active',
                                        cursor: 'help'
                                    }} 
                                />
                            </Tooltip>
                        )}
                    </Stack>
                    {details && (
                        <Typography 
                            variant="caption" 
                            sx={{ 
                                display: 'block',
                                mt: 0.5,
                                color: 'text.secondary'
                            }}
                        >
                            {details}
                        </Typography>
                    )}
                </Box>
            )}
            <Box 
                sx={{
                    width: '100%',
                    ...(align === 'center' && {
                        textAlign: 'center'
                    }),
                    ...(align === 'right' && {
                        textAlign: 'right'
                    })
                }}
            >
                {children}
            </Box>
        </Box>
    );
};

export default FormItem;

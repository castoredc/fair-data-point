import React from 'react';
import { Backdrop, CircularProgress, SxProps, Theme } from '@mui/material';

interface LoadingOverlayProps {
    accessibleLabel?: string;
}

const LoadingOverlay: React.FC<LoadingOverlayProps> = () => {
    const backdropSx: SxProps<Theme> = {
        color: '#fff',
        zIndex: (theme) => theme.zIndex.drawer + 1, // Ensures it appears above other elements
        // backgroundColor: rgba(0, 0, 0, 0.5),
    };

    return (
        <Backdrop sx={backdropSx} open={true}>
            <CircularProgress color="inherit" />
        </Backdrop>
    );
};

export default LoadingOverlay;
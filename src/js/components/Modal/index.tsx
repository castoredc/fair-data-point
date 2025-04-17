import React from 'react';
import { Box, IconButton, Modal as MuiModal, Paper, Typography } from '@mui/material';
import CloseIcon from '@mui/icons-material/Close';
import LoadingOverlay from 'components/LoadingOverlay';

interface ModalProps {
    open: boolean;
    title: string;
    onClose: () => void;
    children: React.ReactNode;
    customWidth?: string;
    isLoading?: boolean;
}

const Modal: React.FC<ModalProps> = ({ open, title, onClose, children, customWidth, isLoading }) => {
    const style = {
        position: 'absolute' as const,
        top: '50%',
        left: '50%',
        transform: 'translate(-50%, -50%)',
        width: customWidth ?? 400,
        p: 4,
    };

    if (open && isLoading) {
        return <LoadingOverlay accessibleLabel="Loading" />;
    }

    return (
        <MuiModal open={open} onClose={onClose} aria-labelledby="modal-title" aria-describedby="modal-description">
            <Paper sx={style}>
                <Box display="flex" justifyContent="space-between" alignItems="center" mb={2}>
                    <Typography id="modal-title" variant="h4">
                        {title}
                    </Typography>
                    <IconButton onClick={onClose}>
                        <CloseIcon />
                    </IconButton>
                </Box>
                <div>
                    {children}
                </div>
            </Paper>
        </MuiModal>
    );
};

export default Modal;
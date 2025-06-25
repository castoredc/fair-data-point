import React from 'react';
import { classNames } from '../../util';
import LoadingOverlay from 'components/LoadingOverlay';
import { Container } from '@mui/material';

interface MainBodyProps {
    children: React.ReactNode;
    isLoading: boolean;
    className?: string;
}

const MainBody: React.FC<MainBodyProps> = ({ children, isLoading, className }) => {
    if (isLoading) {
        return <LoadingOverlay accessibleLabel="Loading" />;
    }

    return <Container
        component="main"
        className={classNames(className)}
    >
        {children}
    </Container>;
};

export default MainBody;

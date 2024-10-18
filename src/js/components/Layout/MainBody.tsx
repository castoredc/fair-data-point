import React from 'react';
import '../../pages/Main/Main.scss';
import { classNames } from '../../util';
import { LoadingOverlay } from '@castoredc/matter';

interface MainBodyProps {
    children: React.ReactNode;
    isLoading: boolean;
    className?: string;
}

const MainBody: React.FC<MainBodyProps> = ({ children, isLoading, className }) => {
    if (isLoading) {
        return <LoadingOverlay accessibleLabel="Loading" content="" />;
    }

    return <main className={classNames('container', className)}>{children}</main>;
};

export default MainBody;
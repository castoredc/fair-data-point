import React, { useEffect } from 'react';
import { RouteComponentProps, withRouter } from 'react-router-dom';
import { classNames } from '../../util';

interface LayoutProps extends RouteComponentProps {
    className?: string;
    embedded?: boolean;
    fullWidth?: boolean;
    children: React.ReactNode;
}

const Layout: React.FC<LayoutProps> = ({ children, className, embedded, fullWidth, location }) => {
    useEffect(() => {
        window.scrollTo(0, 0);
    }, [location.pathname]); // Run this effect whenever the pathname changes

    return <div className={classNames('MainApp', className, embedded && 'Embedded', fullWidth && 'FullWidthApp')}>{children}</div>;
};

export default withRouter(Layout);

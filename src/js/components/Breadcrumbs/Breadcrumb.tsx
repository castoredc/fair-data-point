import React from 'react';
import './Breadcrumbs.scss';
import { Link } from 'react-router-dom';

interface BreadcrumbProps {
    title: string;
    to?: {
        pathname: string;
        state?: any;
    };
}

const Breadcrumb: React.FC<BreadcrumbProps> = ({ title, to }) => {
    return (
        <div className="Breadcrumb">
            {to ? (
                /* @ts-ignore */
                <Link to={{ pathname: to.pathname, state: to.state }}>
                    {title}
                </Link>
            ) : (
                title
            )}
        </div>
    );
};

export default Breadcrumb;
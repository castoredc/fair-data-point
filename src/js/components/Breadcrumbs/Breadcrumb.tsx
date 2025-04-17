import React from 'react';
import './Breadcrumbs.scss';
import { Link as RouterLink } from 'react-router-dom';
import Link from '@mui/material/Link';

interface BreadcrumbProps {
    title: string;
    to?: {
        pathname: string;
        state?: any;
    };
}

const Breadcrumb: React.FC<BreadcrumbProps> = ({ title, to }) => {
    return to ? (
                /* @ts-ignore */
                <Link
                    underline="hover"
                    color="inherit"
                    component={RouterLink}
                    to={{ pathname: to.pathname, state: to.state }}
                >
                    {title}
                </Link>
            ) : (
                <Link
                    underline="hover"
                    color="inherit"
                >
                    {title}
                </Link>
            )
};

export default Breadcrumb;

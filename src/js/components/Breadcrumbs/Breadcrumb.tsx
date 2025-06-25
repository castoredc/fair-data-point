import React from 'react';
import { Link as RouterLink } from 'react-router-dom';
import { Link, Typography } from '@mui/material';

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
            sx={{
                maxWidth: 250,
                whiteSpace: 'nowrap',
                overflow: 'hidden',
                textOverflow: 'ellipsis',
                display: 'block',
                fontSize: 14,
            }}
        >
            {title}
        </Link>
    ) : (
        <Typography
            color="text.secondary"
            sx={{
                maxWidth: 250,
                whiteSpace: 'nowrap',
                overflow: 'hidden',
                textOverflow: 'ellipsis',
                display: 'block',
            }}
        >
            {title}
        </Typography>
    );
};

export default Breadcrumb;

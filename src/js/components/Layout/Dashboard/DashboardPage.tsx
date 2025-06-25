import React, { FC } from 'react';
import './Dashboard.scss';
import { Box } from '@mui/material';

type DashboardPageProps = {
    children: React.ReactNode;
};

const DashboardPage: FC<DashboardPageProps> = ({ children }) => {
    return <Box sx={{ display: 'flex' }}>
        {children}
    </Box>;
};

export default DashboardPage;

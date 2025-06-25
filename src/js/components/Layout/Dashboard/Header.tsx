import React, { FC, ReactElement } from 'react';
import './Dashboard.scss';
import Stack from '@mui/material/Stack';
import { Box } from '@mui/material';
import Typography from '@mui/material/Typography';

type HeaderProps = {
    title: string;
    badge?: ReactElement;
    fullWidth?: boolean;
    children?: React.ReactNode
};

const Header: FC<HeaderProps> = ({ title, badge, children }) => {
    return (
        <Box sx={{ width: '100%', maxWidth: { sm: '100%', md: '1700px' }, pt: 3, pb: 3 }}>
            <Stack direction="row" sx={{ justifyContent: 'space-between' }}>
                <Typography variant="h4">
                    {title}
                    {badge && badge}
                </Typography>

                <div className="HeaderActions">{children}</div>
            </Stack>
        </Box>
    );
};

export default Header;

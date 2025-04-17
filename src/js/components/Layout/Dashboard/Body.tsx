import React, { FC } from 'react';
import './Dashboard.scss';
import { Box } from '@mui/material';
import Stack from '@mui/material/Stack';

type BodyProps = {
    children: React.ReactNode;
};

const Body: FC<BodyProps> = ({ children }) => {
    return <Box
        component="main"
        sx={(theme) => ({
            flexGrow: 1,
            backgroundColor: theme.palette.background.default,
            overflow: 'auto',
        })}
    >
        <Stack
            spacing={2}
            sx={{
                alignItems: 'center',
                mx: 3,
                pb: 5,
                mt: { xs: 8, md: 0 },
            }}
        >
            {children}
        </Stack>
    </Box>;
};

export default Body;

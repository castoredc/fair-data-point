import React, { FC } from 'react';
import { Box } from '@mui/material';

type PageBodyProps = {
    children: React.ReactNode;
};

const PageBody: FC<PageBodyProps> = ({ children }) => {
    return <Box sx={{ width: '100%', maxWidth: { sm: '100%', md: '1700px' } }}>
        {children}
    </Box>;
};

export default PageBody;

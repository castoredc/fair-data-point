import React, { FC } from 'react';
import DocumentTitle from 'components/DocumentTitle';
import BackButton from 'components/BackButton';
import Stack from '@mui/material/Stack';
import Alert from '@mui/material/Alert';

interface NoPermissionProps {
    text: string;
}

const NoPermission: FC<NoPermissionProps> = ({ text }) => {
    return (
        <div style={{ marginLeft: 'auto', marginRight: 'auto' }}>
            <DocumentTitle title="Unauthorized" />

            <Stack direction="row" sx={{ justifyContent: 'center' }}>
                <div style={{ width: '48rem', marginTop: '3.2rem' }}>
                    <BackButton returnButton>Back to previous page</BackButton>

                    <Alert severity="error">
                        {text}
                    </Alert>
                </div>
            </Stack>
        </div>
    );
};
export default NoPermission;

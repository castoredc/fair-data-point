import React from 'react';
import DocumentTitle from 'components/DocumentTitle';
import BackButton from 'components/BackButton';
import Stack from '@mui/material/Stack';
import Alert from '@mui/material/Alert';

export default () => (
    <div style={{ marginLeft: 'auto', marginRight: 'auto' }}>
        <DocumentTitle title="Page not found" />

        <Stack direction="row" sx={{ justifyContent: 'center' }}>
            <div style={{ width: '48rem', marginTop: '3.2rem' }}>
                <BackButton returnButton>Back to previous page</BackButton>

                <Alert severity="info">
                    We could not find this page
                </Alert>
            </div>
        </Stack>
    </div>
);

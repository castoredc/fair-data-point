import React, { FC } from 'react';
import { localizedText } from '../../util';
import { Box, Button, Container, Stack, Typography } from '@mui/material';
import { LoginViews } from 'components/MetadataItem/EnumMappings';
import { CatalogBrandType } from 'types/CatalogType';
import { ServerType } from 'types/ServerType';
import { Field, Form, Formik } from 'formik';
import SelectableListItems from 'components/Input/Formik/SelectableListItems';

interface LoginFormProps {
    catalog?: CatalogBrandType;
    path?: string;
    servers: ServerType[];
    selectedServerId?: string;
    serverLocked?: boolean;
    modal?: boolean;
    brand?: string;
    view?: string;
}

const LoginForm: FC<LoginFormProps> = ({
    catalog,
    path,
    servers,
    selectedServerId,
    serverLocked = false,
    modal = false,
    brand = 'FAIR Data Point',
    view,
}) => {
    const serverIds = servers.map(server => server.id);
    const defaultServer = servers.filter(server => server.default)[0].id;

    return (
        <Formik
            initialValues={{
                server: selectedServerId && serverIds.includes(selectedServerId) ? selectedServerId : defaultServer,
            }}
            onSubmit={values => {
                window.location.href = '/connect/castor/' + values.server + (path ? '?target_path=' + path : '');
            }}
        >
            {({ values, errors, touched, handleChange, handleBlur, handleSubmit, isSubmitting, setValues }) => {
                const viewName = view ? LoginViews[view] : LoginViews['generic'];

                return (
                    <Form>
                        <Container maxWidth="sm">
                            <Stack spacing={4}>
                                {catalog ? (
                                    <Box>
                                        {!modal && (
                                            <Typography 
                                                variant="h4" 
                                                component="h1"
                                                sx={{ 
                                                    mb: 3,
                                                    fontWeight: 500,
                                                    color: 'text.primary'
                                                }}
                                            >
                                                {localizedText(catalog.name, 'en')}
                                            </Typography>
                                        )}

                                        <Stack spacing={2}>
                                            <Typography>
                                                To enter your study in the {localizedText(catalog.name, 'en')} you must be a
                                                registered user.
                                            </Typography>
                                            <Typography>
                                                Please log in with your account and allow the application to
                                                access your information.
                                            </Typography>
                                            {!catalog.accessingData && (
                                                <Typography>
                                                    The application only accesses high-level information from your study and
                                                    will not download nor upload any data to your study.
                                                </Typography>
                                            )}
                                        </Stack>
                                    </Box>
                                ) : (
                                    <Box>
                                        {!modal && (
                                            <Typography 
                                                variant="h4" 
                                                component="h1"
                                                sx={{ 
                                                    mb: 3,
                                                    fontWeight: 500,
                                                    color: 'text.primary'
                                                }}
                                            >
                                                {brand}
                                            </Typography>
                                        )}

                                        <Stack spacing={2}>
                                            {!modal && (
                                                <Typography>
                                                    You need to be a registered user in order to access this {viewName}.
                                                </Typography>
                                            )}
                                            {modal && view !== 'generic' && view !== null && (
                                                <Typography>
                                                    You need to be a registered user in order to access this {viewName}.
                                                </Typography>
                                            )}
                                            <Typography>
                                                Please log in with your account and allow the application to
                                                access your information.
                                            </Typography>
                                        </Stack>
                                    </Box>
                                )}

                                {!serverLocked && (
                                    <Box 
                                        sx={{
                                            bgcolor: 'background.paper',
                                            borderRadius: 1,
                                            p: 3
                                        }}
                                    >
                                        <Typography 
                                            sx={{ 
                                                mb: 2,
                                                fontWeight: 500,
                                                color: 'text.primary'
                                            }}
                                        >
                                            My study is located on a Castor server in
                                        </Typography>
                                        <Field
                                            component={SelectableListItems}
                                            name="server"
                                            options={servers.map(server => ({
                                                title: server.name,
                                                value: server.id,
                                                customIcon: 'flag' + server.flag.toUpperCase(),
                                            }))}
                                        />
                                    </Box>
                                )}

                                <Box sx={{ textAlign: 'center' }}>
                                    <Button
                                        type="submit"
                                        variant="contained"
                                        size="large"
                                        disabled={values.server === null}
                                        sx={{ 
                                            minWidth: 200,
                                            py: 1.5
                                        }}
                                    >
                                        Log in with Castor
                                    </Button>
                                </Box>
                            </Stack>
                        </Container>
                    </Form>
                );
            }}
        </Formik>
    );
};

export default LoginForm;

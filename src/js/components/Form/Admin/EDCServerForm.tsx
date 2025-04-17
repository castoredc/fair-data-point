import React, { useState } from 'react';

import Button from '@mui/material/Button';

import FormItem from './../FormItem';
import { mergeData } from '../../../util';
import { Field, Form, FormikProvider, useFormik } from 'formik';
import Input from 'components/Input/Formik/Input';
import SingleChoice from 'components/Input/Formik/SingleChoice';
import { apiClient } from 'src/js/network';
import { ServerType } from 'types/ServerType';
import { EDCServerDefaultData, EDCServerSchema } from 'components/Form/Admin/form';
import Stack from '@mui/material/Stack';
import { useNotifications } from 'components/WithNotifications';

interface EDCServerFormProps {
    edcServer?: ServerType;
    handleSubmit: Function;
}

const EDCServerForm = (props: EDCServerFormProps) => {
    const [initialValues, setInitialValues] = useState(props.edcServer ? mergeData(EDCServerDefaultData, props.edcServer) : EDCServerDefaultData);
    const [validation, setValidation] = useState();
    const [isSubmitting, setIsSubmitting] = useState(false);
    const notifications = useNotifications();

    const handleFormSubmit = (values, { setSubmitting }) => {
        // setIsSubmitting updates our internal EDCServerForm state, setSubmitting is Formik's.
        setIsSubmitting(true);
        setSubmitting(true);

        apiClient
            .put('/api/castor/servers', values)
            .then(response => {
                setSubmitting(false);
                setIsSubmitting(false);

                // New server:
                if (!props.edcServer && response.data.id) {
                    const message = `The EDC Server ${response.data.name} was saved successfully with id ${response.data.id}`;
                    notifications.show(message, {
                        variant: 'success',

                    });

                    props.handleSubmit(response.data);

                    return;
                }

                // Existing server:
                if (props.edcServer && props.edcServer.id) {
                    const message = `The EDC Server ${response.data.name} was updated successfully`;
                    notifications.show(message, {
                        variant: 'success',

                    });

                    props.handleSubmit(response.data);

                    return;
                }
            })
            .catch(error => {
                setSubmitting(false);
                setIsSubmitting(false);

                if (error.response && error.response.status === 400) {
                    setValidation(error.response.data.fields);
                } else {
                    notifications.show('An error occurred', { variant: 'error' });
                }
            });
    };

    const formProps = useFormik({
        initialValues,
        enableReinitialize: true,
        onSubmit: handleFormSubmit,
        validationSchema: EDCServerSchema,
        validateOnChange: false,
    });

    const form = (
        <FormikProvider value={formProps}>
            <Form>
                <div className="FormContent">
                    <FormItem label="Name">
                        <Field component={Input} name="name" serverError={validation} />
                    </FormItem>
                    <FormItem label="URL">
                        <Field component={Input} name="url" serverError={validation} />
                    </FormItem>
                    <FormItem label="Flag">
                        <Field component={Input} name="flag" serverError={validation} />
                    </FormItem>

                    <FormItem>
                        <Field component={SingleChoice} labelText="Default server?" name="default" />
                    </FormItem>
                    <FormItem label="Client ID">
                        <Field component={Input} name="clientId" serverError={validation} />
                    </FormItem>
                    <FormItem label="Client secret">
                        <Field component={Input} name="clientSecret" serverError={validation} />
                    </FormItem>
                </div>

                {props.edcServer ? (
                    <div className="FormButtons">
                        <Stack direction="row" sx={{ justifyContent: 'space-between' }}>
                            <Button
                                disabled={isSubmitting}
                                type="submit"
                                variant="contained"
                            >
                                Update EDC server
                            </Button>
                        </Stack>
                    </div>
                ) : (
                    <footer>
                        <Button
                            disabled={isSubmitting}
                            type="submit"
                            variant="contained"
                        >
                            Add EDC server
                        </Button>
                    </footer>
                )}
            </Form>
        </FormikProvider>
    );

    return form;
};

export { EDCServerForm };

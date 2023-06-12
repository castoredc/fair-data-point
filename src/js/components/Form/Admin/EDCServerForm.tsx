import React, { useState } from 'react';

import '../Form.scss';
import { toast } from 'react-toastify';
import ToastContent from '../../ToastContent';
import FormItem from './../FormItem';
import { mergeData } from '../../../util';
import { Button, Stack } from '@castoredc/matter';
import { Field, Form, Formik, FormikProvider, useFormik } from 'formik';
import Input from 'components/Input/Formik/Input';
import SingleChoice from 'components/Input/Formik/SingleChoice';
import { apiClient } from 'src/js/network';
import { ServerType } from 'types/ServerType';
import { EDCServerDefaultData, EDCServerSchema } from 'components/Form/Admin/form';

interface EDCServerFormProps {
    edcServer?: ServerType;
    handleSubmit: Function;
}

const EDCServerForm = (props: EDCServerFormProps) => {
    const [initialValues, setInitialValues] = useState(props.edcServer ? mergeData(EDCServerDefaultData, props.edcServer) : EDCServerDefaultData);
    const [validation, setValidation] = useState();
    const [isSubmitting, setIsSubmitting] = useState(false);

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
                    toast.success(<ToastContent type="success" message={message} />, {
                        position: 'top-right',
                    });

                    props.handleSubmit(response.data);

                    return;
                }

                // Existing server:
                if (props.edcServer && props.edcServer.id) {
                    const message = `The EDC Server ${response.data.name} was updated successfully`;
                    toast.success(<ToastContent type="success" message={message} />, {
                        position: 'top-right',
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
                    toast.error(<ToastContent type="error" message="An error occurred" />);
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
                        <Stack distribution="trailing">
                            <Button disabled={isSubmitting} type="submit">
                                Update EDC server
                            </Button>
                        </Stack>
                    </div>
                ) : (
                    <footer>
                        <Button disabled={isSubmitting} type="submit">
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

import React, { Component } from 'react';

import Button from '@mui/material/Button';

import FormItem from './../FormItem';
import { mergeData } from '../../../util';
import * as H from 'history';
import { Field, Form, Formik } from 'formik';
import Input from 'components/Input/Formik/Input';
import * as Yup from 'yup';
import { apiClient } from 'src/js/network';
import PageBody from 'components/Layout/Dashboard/PageBody';
import Stack from '@mui/material/Stack';
import withNotifications, { ComponentWithNotifications } from 'components/WithNotifications';

interface MetadataModelFormProps extends ComponentWithNotifications {
    metadataModel?: any;
    history: H.History;
}

interface MetadataModelFormState {
    initialValues: any;
    validation?: any;
}

class MetadataModelForm extends Component<MetadataModelFormProps, MetadataModelFormState> {
    constructor(props) {
        super(props);

        this.state = {
            initialValues: props.metadataModel ? mergeData(defaultData, props.metadataModel) : defaultData,
            validation: {},
        };
    }

    handleSubmit = (values, { setSubmitting }) => {
        const { metadataModel, history, notifications } = this.props;

        apiClient
            .post('/api/metadata-model' + (metadataModel ? '/' + metadataModel.id : ''), values)
            .then(response => {
                setSubmitting(false);

                if (metadataModel) {
                    notifications.show('The metadata model details are saved successfully', {
                        variant: 'success',

                    });
                } else {
                    history.push('/dashboard/metadata-models/' + response.data.id);
                }
            })
            .catch(error => {
                setSubmitting(false);

                if (error.response && error.response.status === 400) {
                    this.setState({
                        validation: error.response.data.fields,
                    });
                } else {
                    notifications.show('An error occurred', { variant: 'error' });
                }
            });
    };

    render() {
        const { initialValues, validation } = this.state;
        const { metadataModel } = this.props;

        return (
            <PageBody>
                <Formik initialValues={initialValues} onSubmit={this.handleSubmit}
                        validationSchema={MetadataModelSchema}>
                    {({
                          values,
                          errors,
                          touched,
                          handleChange,
                          handleBlur,
                          handleSubmit,
                          isSubmitting,
                          setValues,
                          setFieldValue,
                      }) => {
                        return (
                            <Form>
                                <div className="FormContent">
                                    <FormItem label="Title">
                                        <Field component={Input} name="title" serverError={validation} />
                                    </FormItem>
                                    <FormItem label="Description">
                                        <Field component={Input} name="description" serverError={validation}
                                               multiline />
                                    </FormItem>
                                </div>

                                {metadataModel ? (
                                    <div className="FormButtons">
                                        <Stack direction="row" sx={{ justifyContent: 'space-between' }}>
                                            <Button
                                                disabled={isSubmitting}
                                                type="submit"
                                                variant="contained"
                                            >
                                                Update metadata model
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
                                            Add metadata model
                                        </Button>
                                    </footer>
                                )}
                            </Form>
                        );
                    }}
                </Formik>
            </PageBody>
        );
    }
}

export const defaultData = {
    title: '',
    description: '',
};

const MetadataModelSchema = Yup.object().shape({
    title: Yup.string().required('Please enter a title'),
    description: Yup.string().nullable(),
});

export default withNotifications(MetadataModelForm);
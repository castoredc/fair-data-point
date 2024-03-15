import React, { Component } from 'react';

import '../Form.scss';
import { toast } from 'react-toastify';
import ToastItem from 'components/ToastItem';
import { Button, Stack } from '@castoredc/matter';
import FormItem from './../FormItem';
import { mergeData } from '../../../util';
import * as H from 'history';
import { Field, Form, Formik } from 'formik';
import Input from 'components/Input/Formik/Input';
import * as Yup from 'yup';
import { apiClient } from 'src/js/network';
import PageBody from 'components/Layout/Dashboard/PageBody';

interface MetadataModelFormProps {
    metadataModel?: any;
    history: H.History;
}

interface MetadataModelFormState {
    initialValues: any;
    validation?: any;
}

export default class MetadataModelForm extends Component<MetadataModelFormProps, MetadataModelFormState> {
    constructor(props) {
        super(props);

        this.state = {
            initialValues: props.metadataModel ? mergeData(defaultData, props.metadataModel) : defaultData,
            validation: {},
        };
    }

    handleSubmit = (values, { setSubmitting }) => {
        const { metadataModel, history } = this.props;

        apiClient
            .post('/api/metadata-model' + (metadataModel ? '/' + metadataModel.id : ''), values)
            .then(response => {
                setSubmitting(false);

                if (metadataModel) {
                    toast.success(<ToastItem type="success"
                                                title="The metadata model details are saved successfully" />, {
                        position: 'top-right',
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
                    toast.error(<ToastItem type="error" title="An error occurred" />);
                }
            });
    };

    render() {
        const { initialValues, validation } = this.state;
        const { metadataModel } = this.props;

        return (
            <PageBody>
                <Formik initialValues={initialValues} onSubmit={this.handleSubmit} validationSchema={MetadataModelSchema}>
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
                                        <Stack distribution="trailing">
                                            <Button disabled={isSubmitting} type="submit">
                                                Update metadata model
                                            </Button>
                                        </Stack>
                                    </div>
                                ) : (
                                    <footer>
                                        <Button disabled={isSubmitting} type="submit">
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

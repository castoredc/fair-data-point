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

interface DataModelFormProps extends ComponentWithNotifications {
    dataModel?: any;
    history: H.History;
}

interface DataModelFormState {
    initialValues: any;
    validation?: any;
}

class DataModelForm extends Component<DataModelFormProps, DataModelFormState> {
    constructor(props) {
        super(props);

        this.state = {
            initialValues: props.dataModel ? mergeData(defaultData, props.dataModel) : defaultData,
            validation: {},
        };
    }

    handleSubmit = (values, { setSubmitting }) => {
        const { dataModel, history, notifications } = this.props;

        apiClient
            .post('/api/data-model' + (dataModel ? '/' + dataModel.id : ''), values)
            .then(response => {
                setSubmitting(false);

                if (dataModel) {
                    notifications.show('The data model details are saved successfully', {
                        variant: 'success',

                    });
                } else {
                    history.push('/dashboard/data-models/' + response.data.id);
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
        const { dataModel } = this.props;

        return (
            <PageBody>
                <Formik initialValues={initialValues} onSubmit={this.handleSubmit} validationSchema={DataModelSchema}>
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
                                <div>
                                    <FormItem label="Title">
                                        <Field component={Input} name="title" serverError={validation} />
                                    </FormItem>
                                    <FormItem label="Description">
                                        <Field component={Input} name="description" serverError={validation}
                                               multiline />
                                    </FormItem>
                                </div>

                                {dataModel ? (
                                    <div>
                                        <Stack direction="row" sx={{ justifyContent: 'space-between' }}>
                                            <Button
                                                disabled={isSubmitting}
                                                type="submit"
                                                variant="contained"
                                            >
                                                Update data model
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
                                            Add data model
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

const DataModelSchema = Yup.object().shape({
    title: Yup.string().required('Please enter a title'),
    description: Yup.string().nullable(),
});

export default withNotifications(DataModelForm);
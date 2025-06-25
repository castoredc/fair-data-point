import React, { Component } from 'react';
import FormItem from 'components/Form/FormItem';
import Button from '@mui/material/Button';
import Modal from 'components/Modal';
import * as Yup from 'yup';
import { Field, Form, Formik } from 'formik';
import Input from 'components/Input/Formik/Input';
import { apiClient } from '../network';
import withNotifications, { ComponentWithNotifications } from 'components/WithNotifications';

interface DataSpecificationPrefixModalProps extends ComponentWithNotifications {
    type: string;
    show: boolean;
    handleClose: () => void;
    data: any;
    onSaved: () => void;
    modelId: string;
    versionId: string;
};

type DataSpecificationPrefixModalState = {
    validation: any;
    initialValues: any;
};

class DataSpecificationPrefixModal extends Component<DataSpecificationPrefixModalProps, DataSpecificationPrefixModalState> {
    constructor(props) {
        super(props);

        this.state = {
            initialValues: props.data ? props.data : defaultData,
            validation: {},
        };
    }

    componentDidUpdate(prevProps, prevState, snapshot) {
        const { show, data } = this.props;

        if (show !== prevProps.show || data !== prevProps.data) {
            this.setState({
                initialValues: data ? data : defaultData,
            });
        }
    }

    handleSubmit = (values, { setSubmitting }) => {
        const { type, modelId, versionId, onSaved, notifications } = this.props;

        apiClient
            .post('/api/' + type + '/' + modelId + '/v/' + versionId + '/prefix' + (values.id ? '/' + values.id : ''), {
                prefix: values.prefix,
                uri: values.uri,
            })
            .then(() => {
                setSubmitting(false);

                onSaved();
            })
            .catch(error => {
                if (error.response && error.response.status === 400) {
                    this.setState({
                        validation: error.response.data.fields,
                    });
                } else {
                    notifications.show('An error occurred', { variant: 'error' });
                }

                setSubmitting(false);
            });
    };

    render() {
        const { show, handleClose } = this.props;
        const { initialValues, validation } = this.state;

        const required = 'This field is required';
        const validUrl = 'Please enter a valid URI';

        const title = initialValues.id ? 'Edit prefix' : 'Add prefix';

        return (
            <Modal open={show} onClose={handleClose} title={title}>
                <Formik initialValues={initialValues} validationSchema={PrefixSchema} onSubmit={this.handleSubmit}
                        enableReinitialize>
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
                                <FormItem label="Prefix">
                                    <Field component={Input} name="prefix" serverError={validation} />
                                </FormItem>

                                <FormItem label="URI">
                                    <Field component={Input} name="uri" serverError={validation} />
                                </FormItem>

                                <Button
                                    type="submit"
                                    disabled={isSubmitting}
                                    variant="contained"
                                >
                                    {values.id ? 'Edit prefix' : 'Add prefix'}
                                </Button>
                            </Form>
                        );
                    }}
                </Formik>
            </Modal>
        );
    }
}

const defaultData = {
    prefix: '',
    uri: '',
};

const PrefixSchema = Yup.object().shape({
    prefix: Yup.string().required('Please enter a prefix'),
    uri: Yup.string().url('Please enter a valid URI').required('Please enter a valid URI'),
});

export default withNotifications(DataSpecificationPrefixModal);
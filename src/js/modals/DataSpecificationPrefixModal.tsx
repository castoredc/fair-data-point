import React, { Component } from 'react';
import FormItem from 'components/Form/FormItem';
import { toast } from 'react-toastify';
import ToastItem from 'components/ToastItem';
import { Button, Modal } from '@castoredc/matter';
import * as Yup from 'yup';
import { Field, Form, Formik } from 'formik';
import Input from 'components/Input/Formik/Input';
import { apiClient } from '../network';

type DataModelPrefixModalProps = {
    show: boolean;
    handleClose: () => void;
    data: any;
    onSaved: () => void;
    modelId: string;
    versionId: string;
};

type DataModelPrefixModalState = {
    validation: any;
    initialValues: any;
};

export default class DataModelPrefixModal extends Component<DataModelPrefixModalProps, DataModelPrefixModalState> {
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
        const { modelId, versionId, onSaved } = this.props;

        apiClient
            .post('/api/data-model/' + modelId + '/v/' + versionId + '/prefix' + (values.id ? '/' + values.id : ''), {
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
                    toast.error(<ToastItem type="error" title="An error occurred" />);
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
            <Modal open={show} onClose={handleClose} title={title} accessibleName={title}>
                <Formik initialValues={initialValues} validationSchema={PrefixSchema} onSubmit={this.handleSubmit} enableReinitialize>
                    {({ values, errors, touched, handleChange, handleBlur, handleSubmit, isSubmitting, setValues, setFieldValue }) => {
                        return (
                            <Form>
                                <FormItem label="Prefix">
                                    <Field component={Input} name="prefix" serverError={validation} />
                                </FormItem>

                                <FormItem label="URI">
                                    <Field component={Input} name="uri" serverError={validation} />
                                </FormItem>

                                <Button type="submit" disabled={isSubmitting}>
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

import React, { Component } from 'react';
import FormItem from 'components/Form/FormItem';
import Button from '@mui/material/Button';
import Modal from 'components/Modal';
import * as Yup from 'yup';
import { Field, Form, Formik } from 'formik';
import Input from 'components/Input/Formik/Input';
import { apiClient } from '../../network';
import OptionGroupOptionInput from 'components/Input/Formik/OptionGroupOptionInput';
import withNotifications, { ComponentWithNotifications } from 'components/WithNotifications';

interface OptionGroupModalProps extends ComponentWithNotifications {
    type: string;
    show: boolean;
    handleClose: () => void;
    data: any;
    onSaved: () => void;
    modelId: string;
    versionId: string;
};

type OptionGroupModalState = {
    validation: any;
    initialValues: any;
};

class OptionGroupModal extends Component<OptionGroupModalProps, OptionGroupModalState> {
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
            .post('/api/' + type + '/' + modelId + '/v/' + versionId + '/option-group' + (values.id ? '/' + values.id : ''), {
                title: values.title,
                description: values.description,
                options: values.options,
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

        const title = initialValues.id ? 'Edit option group' : 'Add option group';

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
                                <FormItem label="Name">
                                    <Field component={Input} name="title" serverError={validation} />
                                </FormItem>
                                <FormItem label="Description">
                                    <Field component={Input} name="description" serverError={validation} multiline />
                                </FormItem>

                                <FormItem label="Options">
                                    <Field component={OptionGroupOptionInput} name="options" serverError={validation} />
                                </FormItem>

                                <Button
                                    type="submit"
                                    disabled={isSubmitting}
                                    variant="contained"
                                >
                                    {values.id ? 'Edit option group' : 'Add option group'}
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
    title: '',
    description: '',
    options: [
        {
            title: '',
            value: '',
        },
    ],
};

const PrefixSchema = Yup.object().shape({
    title: Yup.string().required('Please enter a title'),
    description: Yup.string().nullable(),
    options: Yup.array()
        .of(
            Yup.object().shape({
                title: Yup.string().required('Please enter a title'),
                value: Yup.string().required('Please enter a value'),
            }),
        )
        .nullable(),
});

export default withNotifications(OptionGroupModal);
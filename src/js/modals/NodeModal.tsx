import React, { Component } from 'react';
import FormItem from 'components/Form/FormItem';
import Button from '@mui/material/Button';
import Modal from 'components/Modal';
import * as Yup from 'yup';
import { Field, Form, Formik } from 'formik';
import Input from 'components/Input/Formik/Input';
import Choice from 'components/Input/Formik/Choice';
import SingleChoice from 'components/Input/Formik/SingleChoice';
import Select from 'components/Input/Formik/Select';
import { mergeData } from '../util';
import { apiClient } from '../network';
import UriInput from 'components/Input/Formik/UriInput';
import { Types } from 'types/Types';
import withNotifications, { ComponentWithNotifications } from 'components/WithNotifications';

interface AddNodeModalProps extends ComponentWithNotifications {
    open: boolean;
    onClose: () => void;
    data: any;
    onSaved: () => void;
    modelId: string;
    versionId: string;
    type: string;
    modelType: string;
    types: Types;
    optionGroups: any;
    prefixes: any;
};

type AddNodeModalState = {
    validation: any;
    initialValues: any;
};

class NodeModal extends Component<AddNodeModalProps, AddNodeModalState> {
    constructor(props) {
        super(props);

        this.state = {
            initialValues: defaultData,
            validation: {},
        };
    }

    componentDidUpdate(prevProps, prevState, snapshot) {
        const { open, data, type, modelType } = this.props;

        let initialValues = data ? mergeData(defaultData, data) : defaultData;

        if (initialValues.value !== '') {
            if (type === 'external') {
                initialValues.value = initialValues.value.value;
            } else if (type === 'literal') {
                initialValues = {
                    ...initialValues,
                    value: initialValues.value.value,
                    dataType: initialValues.value.dataType,
                };
            } else if (type === 'value') {
                initialValues = {
                    ...initialValues,
                    value: initialValues.value.value,
                    dataType: initialValues.value.dataType,
                };
            }
        }

        if (open !== prevProps.open) {
            this.setState({
                initialValues: {
                    ...initialValues,
                    type: type,
                    modelType: modelType,
                },
            });
        }
    }

    handleSubmit = (values, { setSubmitting }) => {
        const { modelType, type, modelId, versionId, onSaved, data, notifications } = this.props;

        apiClient
            .post('/api/' + modelType + '/' + modelId + '/v/' + versionId + '/node/' + type + (data ? `/${data.id}` : ''), values)
            .then(response => {
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
        const { modelType, data, type, open, onClose, types, optionGroups, prefixes } = this.props;
        const { validation, initialValues } = this.state;

        const title = data ? `Edit ${type} node` : `Add ${type} node`;

        return (
            <Modal open={open} title={title} onClose={onClose}>
                <Formik initialValues={initialValues} validationSchema={NodeSchema} onSubmit={this.handleSubmit}
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
                        const isPlainValue = values.type === 'value' && values.value === 'plain';
                        const isAnnotatedValue = values.type === 'value' && values.value === 'annotated';

                        const showDataTypes = values.type === 'literal' || isPlainValue;
                        const showRepeated =
                            (modelType === 'data-model' && (values.type === 'internal' || values.type === 'value')) ||
                            (modelType === 'metadata-model' && values.type === 'internal');

                        return (
                            <Form>
                                <FormItem label="Title">
                                    <Field component={Input} name="title" serverError={validation} />
                                </FormItem>
                                <FormItem label="Description">
                                    <Field component={Input} name="description" serverError={validation}
                                           multiline={true} />
                                </FormItem>
                                {values.type === 'external' && (
                                    <FormItem label="URI">
                                        <Field component={UriInput} name="value" serverError={validation}
                                               prefixes={prefixes} />
                                    </FormItem>
                                )}
                                {values.type === 'internal' && (
                                    <FormItem label="Slug">
                                        <Field component={Input} name="value" serverError={validation} />
                                    </FormItem>
                                )}

                                {values.type === 'value' && (
                                    <FormItem label="Value">
                                        <Field
                                            component={Choice}
                                            options={[
                                                { value: 'plain', label: 'Plain value' },
                                                { value: 'annotated', label: 'Annotated value' },
                                            ]}
                                            serverError={validation}
                                            name="value"
                                            collapse
                                        />
                                    </FormItem>
                                )}

                                {showDataTypes && (
                                    <FormItem label="Data type">
                                        <Field component={Select} options={types.dataTypes} serverError={validation}
                                               name="dataType" />
                                    </FormItem>
                                )}

                                {values.type === 'literal' && (
                                    <FormItem label="Value">
                                        <Field component={Input} name="value" serverError={validation} />
                                    </FormItem>
                                )}

                                {showRepeated && (
                                    <FormItem>
                                        <Field
                                            component={SingleChoice}
                                            label="Repeated"
                                            serverError={validation}
                                            name="repeated"
                                            details="This node should be repeated for every instance of a specific survey or report"
                                        />
                                    </FormItem>
                                )}

                                <Button type="submit" variant="contained" disabled={isSubmitting}>
                                    {title}
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
    type: '',
    title: '',
    description: '',
    value: '',
    dataType: '',
    repeated: false,
};

const NodeSchema = Yup.object().shape({
    type: Yup.string().required(),
    title: Yup.string().required('Please enter a title'),
    description: Yup.string().nullable(),
    value: Yup.string()
        .required('Please enter a value')
        .when('type', {
            is: 'external',
            then: Yup.string().url('Please enter a valid URL'),
        }),
    repeated: Yup.boolean().when(['modelType', 'type'], {
        is: (modelType, type) => modelType === 'data-model' && (type === 'internal' || type === 'value'),
        then: schema => schema.required('Please select if this node should be repeated'),
    }),
    dataType: Yup.string().when(['type', 'value'], {
        is: (type, value) => type === 'literal' || (type === 'value' && value === 'plain'),
        then: schema => schema.required('Please select a data type'),
    }),
});

export default withNotifications(NodeModal);
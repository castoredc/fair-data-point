import React, { Component } from 'react';
import FormItem from 'components/Form/FormItem';
import { toast } from 'react-toastify';
import ToastItem from 'components/ToastItem';
import { Button, Modal } from '@castoredc/matter';
import { DataType } from 'components/MetadataItem/EnumMappings';
import * as Yup from 'yup';
import { Field, Form, Formik } from 'formik';
import Input from 'components/Input/Formik/Input';
import Choice from 'components/Input/Formik/Choice';
import SingleChoice from 'components/Input/Formik/SingleChoice';
import Select from 'components/Input/Formik/Select';
import { mergeData } from '../util';
import { apiClient } from '../network';

type AddNodeModalProps = {
    open: boolean;
    onClose: () => void;
    data: any;
    onSaved: () => void;
    modelId: string;
    versionId: string;
    type: string;
    modelType: string;
    types: {
        fieldTypes: {
            plain: {
                value: string,
                label: string
            }[],
            annotated: {
                value: string,
                label: string
            }[]
        },
        dataTypes: {
            value: string,
            label: string
        }[],
    };
};

type AddNodeModalState = {
    validation: any;
    initialValues: any;
};

export default class NodeModal extends Component<AddNodeModalProps, AddNodeModalState> {
    constructor(props) {
        super(props);

        this.state = {
            initialValues: defaultData,
            validation: {},
        };
    }

    componentDidUpdate(prevProps, prevState, snapshot) {
        const { open, data, type } = this.props;

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
                    fieldType: initialValues.value.fieldType,
                    optionGroup: initialValues.value.optionGroup
                };
            }
        }

        if (open !== prevProps.open) {
            this.setState({
                initialValues: {
                    ...initialValues,
                    type: type,
                },
            });
        }
    }

    handleSubmit = (values, { setSubmitting }) => {
        const { modelType, type, modelId, versionId, onSaved, data } = this.props;

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
                    toast.error(<ToastItem type="error" title="An error occurred" />);
                }
                setSubmitting(false);
            });
    };

    render() {
        const { modelType, data, type, open, onClose, types } = this.props;
        const { validation, initialValues } = this.state;

        const title = data ? `Edit ${type} node` : `Add ${type} node`;

        return (
            <Modal accessibleName={title} open={open} title={title} onClose={onClose}>
                <Formik initialValues={initialValues} validationSchema={NodeSchema} onSubmit={this.handleSubmit} enableReinitialize>
                    {({ values, errors, touched, handleChange, handleBlur, handleSubmit, isSubmitting, setValues, setFieldValue }) => {
                        const isPlainValue = (values.type === 'value' && values.value === 'plain');
                        const isAnnotatedValue = (values.type === 'value' && values.value === 'annotated');

                        const showDataTypes = values.type === 'literal' || isPlainValue;
                        const showRepeated = modelType === 'data-model' && (values.type === 'internal' || values.type === 'value');

                        const showFieldTypes = modelType === 'metadata-model' && (isPlainValue || isAnnotatedValue);
                        console.log(values.type);
                        console.log(values.dataType);

                        let fieldTypes = [];
                        if(showFieldTypes && isPlainValue) {
                            fieldTypes = values.dataType ? types.fieldTypes[values.value][values.dataType] : [];
                        } else if(showFieldTypes && isAnnotatedValue) {
                            fieldTypes = types.fieldTypes[values.value];
                        }

                        return (
                            <Form>
                                <FormItem label="Title">
                                    <Field component={Input} name="title" serverError={validation} />
                                </FormItem>
                                <FormItem label="Description">
                                    <Field component={Input} name="description" serverError={validation} multiline={true} />
                                </FormItem>
                                {values.type === 'external' && (
                                    <FormItem label="URI">
                                        <Field component={Input} name="value" serverError={validation} />
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
                                                { value: 'plain', labelText: 'Plain value' },
                                                { value: 'annotated', labelText: 'Annotated value' },
                                            ]}
                                            serverError={validation}
                                            name="value"
                                            collapse
                                        />
                                    </FormItem>
                                )}

                                {showDataTypes && (
                                    <FormItem label="Data type">
                                        <Field component={Select} options={types.dataTypes} serverError={validation} name="dataType" />
                                    </FormItem>
                                )}

                                {showFieldTypes && (
                                    <FormItem label="Field type">
                                        <Field component={Select} options={fieldTypes} serverError={validation} name="fieldType" />
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
                                            labelText="Repeated"
                                            serverError={validation}
                                            name="repeated"
                                            details="This node should be repeated for every instance of a specific survey or report"
                                        />
                                    </FormItem>
                                )}

                                <Button buttonType="primary" type="submit" disabled={isSubmitting}>
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
    repeated: Yup.boolean().required('Please select if this node should be repeated'),
    dataType: Yup.string().when(['type', 'value'], {
        is: (type, value) => type === 'literal' || (type === 'value' && value === 'plain'),
        then: schema => schema.required('Please select a data type'),
    }),
});

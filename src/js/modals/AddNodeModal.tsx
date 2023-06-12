import React, { Component } from 'react';
import FormItem from 'components/Form/FormItem';
import { toast } from 'react-toastify';
import ToastContent from 'components/ToastContent';
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
};

type AddNodeModalState = {
    validation: any;
    initialValues: any;
};

export default class AddNodeModal extends Component<AddNodeModalProps, AddNodeModalState> {
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
            } else if (type === 'literal' || type === 'value') {
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
                },
            });
        }
    }

    handleSubmit = (values, { setSubmitting }) => {
        const { type, modelId, versionId, onSaved, data } = this.props;

        apiClient
            .post('/api/model/' + modelId + '/v/' + versionId + '/node/' + type + (data ? `/${data.id}` : ''), values)
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
                    toast.error(<ToastContent type="error" message="An error occurred" />);
                }
                setSubmitting(false);
            });
    };

    render() {
        const { data, type, open, onClose } = this.props;
        const { validation, initialValues } = this.state;

        const title = data ? `Edit ${type} node` : `Add ${type} node`;

        return (
            <Modal accessibleName={title} open={open} title={title} onClose={onClose}>
                <Formik initialValues={initialValues} validationSchema={NodeSchema} onSubmit={this.handleSubmit} enableReinitialize>
                    {({ values, errors, touched, handleChange, handleBlur, handleSubmit, isSubmitting, setValues, setFieldValue }) => {
                        const showDataTypes = values.type === 'literal' || (values.type === 'value' && values.value === 'plain');
                        const showRepeated = values.type === 'internal' || values.type === 'value';

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

                                {showDataTypes && (
                                    <FormItem label="Data type">
                                        <Field component={Select} options={dataTypes} serverError={validation} name="dataType" />
                                    </FormItem>
                                )}

                                {values.type === 'literal' && (
                                    <FormItem label="Value">
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
                                {showRepeated && (
                                    <FormItem>
                                        <Field component={SingleChoice} labelText="Repeated" serverError={validation} name="repeated" />
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

const dataTypes = [
    { value: 'float', label: DataType['float'] },
    { value: 'double', label: DataType['double'] },
    { value: 'decimal', label: DataType['decimal'] },
    { value: 'integer', label: DataType['integer'] },
    { value: 'dateTime', label: DataType['dateTime'] },
    { value: 'date', label: DataType['date'] },
    { value: 'time', label: DataType['time'] },
    { value: 'gDay', label: DataType['gDay'] },
    { value: 'gMonth', label: DataType['gMonth'] },
    { value: 'gYear', label: DataType['gYear'] },
    { value: 'gYearMonth', label: DataType['gYearMonth'] },
    { value: 'gMonthDay', label: DataType['gMonthDay'] },
    { value: 'string', label: DataType['string'] },
    { value: 'boolean', label: DataType['boolean'] },
];

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

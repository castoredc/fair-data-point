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
import { getType, mergeData } from '../util';
import { apiClient } from '../network';

type FieldModalProps = {
    open: boolean;
    onClose: () => void;
    data: any;
    onSaved: () => void;
    modelId: string;
    versionId: string;
    types: {
        fieldTypes: {
            plain: {
                [key: string]: {
                    value: string,
                    label: string
                }[],
            },
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
    nodes: any;
    optionGroups: any;
    form: any;
};

type FieldModalState = {
    validation: any;
    initialValues: any;
};

export default class FieldModal extends Component<FieldModalProps, FieldModalState> {
    constructor(props) {
        super(props);

        this.state = {
            initialValues: defaultData,
            validation: {},
        };
    }

    componentDidUpdate(prevProps, prevState, snapshot) {
        const { open, data, nodes } = this.props;

        let initialValues = data ? mergeData(defaultData, data) : defaultData;

        const selectedNode = nodes.value.find((node) => node.id === initialValues.node);

        if (open !== prevProps.open) {
            this.setState({
                initialValues: {
                    ...initialValues,
                    nodeData: selectedNode ? selectedNode.value : null,
                },
            });
        }
    }

    getOrderOptions = () => {
        const { data, form } = this.props;

        let order = [{
            value: 1,
            label: 'At the beginning',
        }];

        if (form.fields.length === 0) {
            return order;
        }

        for (let i = 0; i < form.fields.length; i++) {
            const item = form.fields[i];

            if (data === null || (data && item.id !== data.id)) {
                const moduleNumber = i + 1;
                order.push({
                    value: moduleNumber + 1,
                    label: 'After field ' + moduleNumber + ' (' + item.title + ')',
                });
            }
        }

        return order;
    };

    handleSubmit = (values, { setSubmitting }) => {
        const { modelId, versionId, onSaved, data, form } = this.props;

        apiClient
            .post('/api/metadata-model/' + modelId + '/v/' + versionId + '/form/' + form.id + '/field' + (data ? `/${data.id}` : ''), values)
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
        const { data, open, onClose, types, optionGroups, nodes } = this.props;
        const { validation, initialValues } = this.state;

        const title = data ? `Edit field` : `Add field`;

        return (
            <Modal accessibleName={title} open={open} title={title} onClose={onClose}>
                <Formik initialValues={initialValues} validationSchema={NodeSchema} onSubmit={this.handleSubmit} enableReinitialize>
                    {({ values, errors, touched, handleChange, handleBlur, handleSubmit, isSubmitting, setValues, setFieldValue }) => {
                        const isPlainValue = values.nodeData && (values.nodeData.value === 'plain');
                        const isAnnotatedValue = values.nodeData && (values.nodeData.value === 'annotated');
                        const orderOptions = this.getOrderOptions();

                        let fieldTypes: { value: string; label: string }[]  = [];

                        if(isPlainValue) {
                            fieldTypes = types.fieldTypes.plain[values.nodeData.dataType];
                        } else if(isAnnotatedValue) {
                            fieldTypes = types.fieldTypes.annotated;
                        }

                        const nodeItems = nodes.value.map((node) => {
                            return {
                                value: node.id,
                                label: node.title,
                            }
                        })

                        const optionGroupItems = optionGroups.map((optionGroup) => {
                            return {
                                value: optionGroup.id,
                                label: optionGroup.title,
                            }
                        });

                        return (
                            <Form>
                                <FormItem label="Title">
                                    <Field component={Input} name="title" serverError={validation} />
                                </FormItem>

                                <FormItem label="Position">
                                    <Field component={Select} options={orderOptions} name="order" serverError={validation} menuPosition="fixed" />
                                </FormItem>

                                <FormItem label="Description">
                                    <Field component={Input} name="description" serverError={validation} multiline={true} />
                                </FormItem>

                                <FormItem label="Node">
                                    <Field component={Select} options={nodeItems} serverError={validation} name="node" onChange={(e) => {
                                        setFieldValue('node', e.value);
                                        setFieldValue('fieldType', '');
                                        setFieldValue('optionGroup', '');

                                        const selectedNode = nodes.value.find((node) => node.id === e.value);
                                        setFieldValue('nodeData', selectedNode.value);
                                    }}/>
                                </FormItem>

                                <FormItem label="Field type">
                                    <Field component={Select} options={fieldTypes} serverError={validation} name="fieldType" onChange={(e) => {
                                        setFieldValue('fieldType', e.value);
                                        setFieldValue('optionGroup', '');
                                    }}/>
                                </FormItem>

                                {isAnnotatedValue && values.fieldType !== 'ontologyConceptBrowser' && (
                                    <FormItem label="Option group">
                                        <Field component={Select} options={optionGroupItems} serverError={validation} name="optionGroup" />
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
    title: '',
    description: '',
    node: '',
    order: '',
    fieldType: '',
    optionGroup: '',
    nodeData: null,
};

const NodeSchema = Yup.object().shape({
    title: Yup.string().required('Please enter a title'),
    description: Yup.string().nullable(),
    node: Yup.string().required('Please select a node'),
    fieldType: Yup.string().required('Please select a field type'),
    optionGroup: Yup.string().when(['nodeData', 'fieldType'], {
        is: (nodeData, fieldType) => (nodeData && nodeData.value === 'annotated') && fieldType !== 'ontologyConceptBrowser',
        then: schema => schema.required('Please select an option group'),
    }),
});
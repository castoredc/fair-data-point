import React, { Component } from 'react';
import FormItem from 'components/Form/FormItem';
import { toast } from 'react-toastify';
import ToastItem from 'components/ToastItem';
import { Button, Modal } from '@castoredc/matter';
import { DataType, ResourceType } from 'components/MetadataItem/EnumMappings';
import * as Yup from 'yup';
import { Field, Form, Formik } from 'formik';
import Input from 'components/Input/Formik/Input';
import Choice from 'components/Input/Formik/Choice';
import SingleChoice from 'components/Input/Formik/SingleChoice';
import Select from 'components/Input/Formik/Select';
import { getType, mergeData } from '../util';
import { apiClient } from '../network';
import UriInput from 'components/Input/Formik/UriInput';
import { Types, ValueLabel } from 'types/Types';

type DisplaySettingModalProps = {
    open: boolean;
    onClose: () => void;
    data: any;
    onSaved: () => void;
    modelId: string;
    versionId: string;
    nodes: any;
    types: Types,
    items: any;
    resourceType: string;
    position: string;
};

type DisplaySettingModalState = {
    validation: any;
    initialValues: any;
};

export default class DisplaySettingModal extends Component<DisplaySettingModalProps, DisplaySettingModalState> {
    constructor(props) {
        super(props);

        this.state = {
            initialValues: defaultData,
            validation: {},
        };
    }


    componentDidUpdate(prevProps, prevState, snapshot) {
        const { open } = this.props;

        if (open !== prevProps.open) {
            this.setState({
                initialValues: this.handleNewData(),
            });
        }
    }

    handleNewData = () => {
        const { data, nodes, position, resourceType } = this.props;

        let newData = {
            ...defaultData,
            nodeData: null,
            position: position,
            resourceType: resourceType,
        };

        if (data !== null) {
            newData = data;
        } else {
            newData.order = this.getOrderOptions().slice(-1)[0].value;
        }

        const selectedNode = nodes.value.find((node) => node.id === newData.node);
        newData.nodeData = selectedNode ? selectedNode.value : null;

        return newData;
    };

    getOrderOptions = () => {
        const { data, items } = this.props;

        let order = [{
            value: 1,
            label: 'At the beginning',
        }];

        if (items.length === 0) {
            return order;
        }

        for (let i = 0; i < items.length; i++) {
            const item = items[i];

            if (data === null || (data && item.id !== data.id)) {
                const itemNumber = i + 1;

                order.push({
                    value: itemNumber + 1,
                    label: 'After item ' + itemNumber + ' (' + item.title + ')',
                });
            }
        }

        return order;
    };

    handleSubmit = (values, { setSubmitting }) => {
        const { modelId, versionId, onSaved, data, resourceType } = this.props;

        apiClient
            .post('/api/metadata-model/' + modelId + '/v/' + versionId + '/display' + (data ? `/${data.id}` : ''), values)
            .then(response => {
                setSubmitting(false);

                onSaved();
            })
            .catch(error => {
                if (error.response && error.response.status === 400) {
                    this.setState({
                        validation: error.response.data.fields,
                    });
                } else if (error.response) {
                    toast.error(<ToastItem type="error" title={error.response.data.error} />);
                } else {
                    toast.error(<ToastItem type="error" title="An error occurred" />);
                }

                setSubmitting(false);
            });
    };

    render() {
        const { data, open, onClose, types, nodes } = this.props;
        const { validation, initialValues } = this.state;

        const title = data ? `Edit setting` : `Add setting`;

        return (
            <Modal accessibleName={title} open={open} title={title} onClose={onClose}>
                <Formik initialValues={initialValues} validationSchema={NodeSchema} onSubmit={this.handleSubmit} enableReinitialize>
                    {({ values, errors, touched, handleChange, handleBlur, handleSubmit, isSubmitting, setValues, setFieldValue }) => {
                        const isPlainValue = values.nodeData && (values.nodeData.value === 'plain');
                        const isAnnotatedValue = values.nodeData && (values.nodeData.value === 'annotated');
                        const orderOptions = this.getOrderOptions();

                        let displayTypes: { value: string; label: string }[]  = [];

                        if(isPlainValue) {
                            displayTypes = types.displayTypes.plain[values.nodeData.dataType];
                        } else if(isAnnotatedValue) {
                            displayTypes = types.displayTypes.annotated;
                        }

                        displayTypes = displayTypes.sort(function(a, b) {
                            return a.label.localeCompare(b.label)
                        });

                        const nodeItems = nodes.value.map((node) => {
                            return {
                                value: node.id,
                                label: node.title,
                            }
                        })

                        return (
                            <Form>
                                <FormItem label="Title">
                                    <Field component={Input} name="title" serverError={validation} />
                                </FormItem>

                                <FormItem label="Position">
                                    <Field component={Select} options={orderOptions} name="order" serverError={validation} menuPosition="fixed" />
                                </FormItem>

                                <FormItem label="Node">
                                    <Field component={Select} options={nodeItems} serverError={validation} name="node" onChange={(e) => {
                                        setFieldValue('node', e.value);
                                        setFieldValue('displayType', '');

                                        const selectedNode = nodes.value.find((node) => node.id === e.value);
                                        setFieldValue('nodeData', selectedNode.value);
                                    }}/>
                                </FormItem>

                                <FormItem label="Display type">
                                    <Field component={Select} options={displayTypes} serverError={validation} name="displayType" />
                                </FormItem>

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
    node: '',
    order: 1,
    displayType: '',
    nodeData: null,
    position: '',
    resourceType: '',
};

const NodeSchema = Yup.object().shape({
    title: Yup.string().required('Please enter a title'),
    node: Yup.string().required('Please select a node'),
    displayType: Yup.string().required('Please select a display type'),
});
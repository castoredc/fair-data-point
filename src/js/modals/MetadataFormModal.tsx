import React, { Component } from 'react';
import FormItem from 'components/Form/FormItem';
import { toast } from 'react-toastify';
import ToastItem from 'components/ToastItem';
import { Button, Modal, Stack } from '@castoredc/matter';
import ConfirmModal from 'modals/ConfirmModal';
import { classNames } from '../util';
import { Field, Form, Formik } from 'formik';
import Input from 'components/Input/Formik/Input';
import Select from 'components/Input/Formik/Select';
import * as Yup from 'yup';
import { apiClient } from '../network';
import { ResourceType } from 'components/MetadataItem/EnumMappings';

type MetadataFormModalProps = {
    show: boolean;
    data: any;
    orderOptions: any;
    type: string;
    modelId: string;
    versionId: string;
    onSaved: () => void;
    handleClose: () => void;
};

type MetadataFormModalState = {
    initialValues: any;
    validation: any;
};

export default class MetadataFormModal extends Component<MetadataFormModalProps, MetadataFormModalState> {
    constructor(props) {
        super(props);

        this.state = {
            initialValues: this.handleNewData(),
            validation: {},
        };
    }

    componentDidUpdate(prevProps, prevState, snapshot) {
        const { show, data } = this.props;

        if (show !== prevProps.show || data !== prevProps.data) {
            this.setState({
                initialValues: this.handleNewData(),
            });
        }
    }

    handleNewData = () => {
        const { data, orderOptions, type } = this.props;

        let newData = {
            ...defaultData,
        };

        if (data !== null) {
            newData = data;
        } else {
            newData.order = orderOptions.slice(-1)[0].value;
        }

        return newData;
    };

    handleSubmit = (values, { setSubmitting }) => {
        const { type, modelId, versionId, onSaved } = this.props;

        apiClient
            .post('/api/' + type + '/' + modelId + '/v/' + versionId + '/form' + (values.id ? '/' + values.id : ''), values)
            .then(() => {
                onSaved();
                setSubmitting(false);
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

    handleDelete = (id, callback) => {
        const { type, modelId, versionId, onSaved } = this.props;

        apiClient
            .delete('/api/' + type + '/' + modelId + '/v/' + versionId + '/form/' + id)
            .then(() => {
                callback();
                onSaved();
            })
            .catch(error => {
                toast.error(<ToastItem type="error" title="An error occurred" />);
            });
    };

    render() {
        const { type, show, handleClose, orderOptions } = this.props;
        const { initialValues, validation } = this.state;

        console.log(resourceTypes);

        const title = initialValues.id ? 'Edit form' : 'Add form';
        return (
            <Modal open={show} onClose={handleClose} title={title} accessibleName={title}>
                <Formik initialValues={initialValues} onSubmit={this.handleSubmit} validationSchema={MetadataModelFormSchema}>
                    {({ values, errors, touched, handleChange, handleBlur, handleSubmit, isSubmitting, setValues, setFieldValue }) => {
                        return (
                            <Form>
                                <FormItem label="Title">
                                    <Field component={Input} name="title" serverError={validation} />
                                </FormItem>

                                <FormItem label="Position">
                                    <Field component={Select} options={orderOptions} name="order" serverError={validation} menuPosition="fixed" />
                                </FormItem>

                                <FormItem label="Type">
                                    <Field component={Select} options={resourceTypes} name="resourceType" serverError={validation} menuPosition="fixed" />
                                </FormItem>

                                <div className={classNames(values.id && 'HasConfirmButton')}>
                                    <Stack alignment="normal" distribution="equalSpacing">
                                        {values.id && (
                                            <ConfirmModal
                                                title="Delete form"
                                                action="Delete form"
                                                variant="danger"
                                                onConfirm={callback => this.handleDelete(values.id, callback)}
                                                includeButton={true}
                                            >
                                                Are you sure you want to delete form <strong>{values.title}</strong>?<br />
                                                This will also delete all associated fields.
                                            </ConfirmModal>
                                        )}
                                        <Button type="submit" disabled={isSubmitting}>
                                            {values.id ? 'Edit form' : 'Add form'}
                                        </Button>
                                    </Stack>
                                </div>
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
    order: '',
    resourceType: '',
};

const MetadataModelFormSchema = Yup.object().shape({
    title: Yup.string().required('Please enter a title'),
    order: Yup.string().required('Please select a position'),
});

const resourceTypes = [
    { value: 'fdp', label: ResourceType.fdp },
    { value: 'catalog', label: ResourceType.catalog },
    { value: 'dataset', label: ResourceType.dataset },
    { value: 'distribution', label: ResourceType.distribution },
    { value: 'study', label: ResourceType.study },
];
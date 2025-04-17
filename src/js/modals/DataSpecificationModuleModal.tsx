import React, { Component } from 'react';
import FormItem from 'components/Form/FormItem';
import Button from '@mui/material/Button';
import ConfirmModal from 'modals/ConfirmModal';
import { classNames, getType } from '../util';
import DependencyModal from 'modals/DependencyModal';
import { Field, Form, Formik } from 'formik';
import Input from 'components/Input/Formik/Input';
import Select from 'components/Input/Formik/Select';
import SingleChoice from 'components/Input/Formik/SingleChoice';
import { NodeType } from 'types/NodeType';
import { PrefixType } from 'types/PrefixType';
import * as Yup from 'yup';
import { apiClient } from '../network';
import { ResourceType } from 'components/MetadataItem/EnumMappings';
import Stack from '@mui/material/Stack';
import Modal from 'components/Modal';
import AltRouteIcon from '@mui/icons-material/AltRoute';
import withNotifications, { ComponentWithNotifications } from 'components/WithNotifications';
import { Alert } from '@mui/material';

interface DataModelModuleModalProps extends ComponentWithNotifications {
    show: boolean;
    data: any;
    orderOptions: any;
    type: string;
    modelId: string;
    versionId: string;
    onSaved: () => void;
    handleClose: () => void;
    valueNodes: NodeType[];
    prefixes: PrefixType[];
};

type DataModelModuleModalState = {
    initialValues: any;
    validation: any;
    showDependencyModal: boolean;
};

class DataSpecificationModuleModal extends Component<DataModelModuleModalProps, DataModelModuleModalState> {
    constructor(props) {
        super(props);

        this.state = {
            initialValues: this.handleNewData(),
            validation: {},
            showDependencyModal: false,
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
            ...(type === 'data-model' && defaultDataModelData),
            ...(type === 'metadata-model' && defaultMetadataModelData),
        };

        if (data !== null) {
            newData = data;
        } else {
            newData.order = orderOptions.slice(-1)[0].value;
        }

        return newData;
    };

    handleSubmit = (values, { setSubmitting }) => {
        const { type, modelId, versionId, onSaved, notifications } = this.props;

        apiClient
            .post('/api/' + type + '/' + modelId + '/v/' + versionId + '/module' + (values.id ? '/' + values.id : ''), values)
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
                    notifications.show('An error occurred', { variant: 'error' });
                }
                setSubmitting(false);
            });
    };

    handleDelete = (id, callback) => {
        const { type, modelId, versionId, onSaved, notifications } = this.props;

        apiClient
            .delete('/api/' + type + '/' + modelId + '/v/' + versionId + '/module/' + id)
            .then(() => {
                callback();
                onSaved();
            })
            .catch(error => {
                notifications.show('An error occurred', { variant: 'error' });
            });
    };

    openDependencyModal = () => {
        this.setState({
            showDependencyModal: true,
        });
    };

    closeDependencyModal = () => {
        this.setState({
            showDependencyModal: false,
        });
    };

    render() {
        const { type, show, handleClose, orderOptions, valueNodes, prefixes } = this.props;
        const { initialValues, validation, showDependencyModal } = this.state;

        const title = initialValues.id ? 'Edit group' : 'Add group';
        const schema = type === 'data-model' ? DataModelModuleSchema : MetadataModelModuleSchema;

        const showDependencies = type === 'metadata-model' || (type === 'data-model' && valueNodes.length > 0);

        return (
            <Modal open={show} onClose={handleClose} title={title}>
                <Formik initialValues={initialValues} onSubmit={this.handleSubmit} validationSchema={schema}>
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
                                <DependencyModal
                                    modelType={type}
                                    show={showDependencyModal}
                                    handleClose={this.closeDependencyModal}
                                    save={dependencies => {
                                        setFieldValue('dependencies', dependencies).then(() => {
                                            this.setState({
                                                showDependencyModal: false,
                                            });
                                        });
                                    }}
                                    valueNodes={valueNodes}
                                    prefixes={prefixes}
                                    dependencies={values.dependencies}
                                />

                                <FormItem label="Title">
                                    <Field component={Input} name="title" serverError={validation} />
                                </FormItem>

                                <FormItem label="Position">
                                    <Field component={Select} options={orderOptions} name="order"
                                           serverError={validation} />
                                </FormItem>

                                {type === 'metadata-model' && (
                                    <>
                                        <FormItem label="Type">
                                            <Field
                                                component={Select}
                                                options={resourceTypes}
                                                name="resourceType"
                                                serverError={validation}

                                            />
                                        </FormItem>
                                    </>
                                )}

                                {type === 'data-model' && (
                                    <>
                                        <FormItem>
                                            <Field
                                                component={SingleChoice}
                                                label="Repeated"
                                                name="repeated"
                                                serverError={validation}
                                                details="This group should be repeated for every instance of a specific survey or report"
                                            />
                                        </FormItem>
                                    </>
                                )}

                                <FormItem>
                                    {!showDependencies ? (
                                        <Alert severity="warning">
                                            There are no value nodes added to this {getType(type)}. <br />
                                            Please add a value node in order to set up dependencies.
                                        </Alert>
                                    ) : (
                                        <>
                                            <Field
                                                component={SingleChoice}
                                                label="Dependent"
                                                name="dependent"
                                                serverError={validation}
                                                details="This group will only be rendered when certain criteria are met"
                                            />

                                            {values.dependent && (
                                                <>
                                                    <Button variant="outlined" onClick={this.openDependencyModal}
                                                            startIcon={<AltRouteIcon />}>
                                                        Edit dependencies
                                                    </Button>
                                                </>
                                            )}
                                        </>
                                    )}
                                </FormItem>

                                <div className={classNames(values.id && 'HasConfirmButton')}>
                                    <Stack direction="row" sx={{ justifyContent: 'space-between' }}>
                                        {values.id && (
                                            <ConfirmModal
                                                title="Delete group"
                                                action="Delete group"
                                                variant="contained"
                                                color="error"
                                                onConfirm={callback => this.handleDelete(values.id, callback)}
                                                includeButton={true}
                                            >
                                                Are you sure you want to delete
                                                group <strong>{values.title}</strong>?<br />
                                                This will also delete all associated triples.
                                            </ConfirmModal>
                                        )}
                                        <Button
                                            type="submit"
                                            disabled={isSubmitting}
                                            variant="contained"
                                        >
                                            {values.id ? 'Edit group' : 'Add group'}
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
    dependent: false,
    dependencies: {
        rules: [],
        combinator: 'and',
        not: false,
    },
};

const defaultDataModelData = {
    repeated: false,
};

const defaultMetadataModelData = {
    resourceType: '',
};

const DataModelModuleSchema = Yup.object().shape({
    title: Yup.string().required('Please enter a title'),
    order: Yup.string().required('Please select a position'),
    repeated: Yup.boolean(),
    dependent: Yup.boolean(),
});

const MetadataModelModuleSchema = Yup.object().shape({
    title: Yup.string().required('Please enter a title'),
    order: Yup.string().required('Please select a position'),
    resourceType: Yup.string().required('Please select a type'),
});

const resourceTypes = [
    { value: 'fdp', label: ResourceType.fdp },
    { value: 'catalog', label: ResourceType.catalog },
    { value: 'dataset', label: ResourceType.dataset },
    { value: 'distribution', label: ResourceType.distribution },
    { value: 'study', label: ResourceType.study },
];

export default withNotifications(DataSpecificationModuleModal);
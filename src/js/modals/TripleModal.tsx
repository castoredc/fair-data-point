import React, { Component } from 'react';
import FormItem from 'components/Form/FormItem';
import Button from '@mui/material/Button';
import Modal from 'components/Modal';
import { PrefixType } from 'types/PrefixType';
import { NodesType } from 'types/NodesType';
import { ModuleType } from 'types/ModuleType';
import { Field, Form, Formik } from 'formik';
import Select from 'components/Input/Formik/Select';
import UriInput from 'components/Input/Formik/UriInput';
import * as Yup from 'yup';
import { apiClient } from '../network';
import Stack from '@mui/material/Stack';
import Box from '@mui/material/Box';
import withNotifications, { ComponentWithNotifications } from 'components/WithNotifications';

interface TripleModalProps extends ComponentWithNotifications {
    type: string;
    show: boolean;
    handleClose: () => void;
    data: any;
    nodes: NodesType;
    prefixes: PrefixType[];
    module: ModuleType;
    onSaved: () => void;
    modelId: string;
    versionId: string;
};

type TripleModalState = {
    initialValues: any;
    validation: any;
};

class TripleModal extends Component<TripleModalProps, TripleModalState> {
    constructor(props) {
        super(props);

        this.state = {
            initialValues: props.data ? props.data : defaultData[props.type],
            validation: {},
        };
    }

    componentDidUpdate(prevProps, prevState, snapshot) {
        const { show, data, type } = this.props;

        if (show !== prevProps.show || data !== prevProps.data) {
            this.setState({
                initialValues: data ? data : defaultData[type],
            });
        }
    }

    handleSubmit = (values, { setSubmitting }) => {
        const { type, modelId, versionId, module, onSaved, notifications } = this.props;

        apiClient
            .post(
                '/api/' + type + '/' + modelId + '/v/' + versionId + '/module/' + module.id + '/triple' + (values.id ? '/' + values.id : ''),
                values,
            )
            .then(response => {
                setSubmitting(false);

                onSaved();
            })
            .catch(error => {
                setSubmitting(false);

                if (error.response && error.response.status === 400) {
                    this.setState({
                        validation: error.response.values.fields,
                    });
                } else {
                    notifications.show('An error occurred', { variant: 'error' });
                }
            });
    };

    getOptions = type => {
        const { nodes } = this.props;

        return nodes[type].map(node => {
            return { value: node.id, label: node.title, repeated: node.repeated };
        });
    };

    render() {
        const { show, handleClose, module, prefixes, type } = this.props;
        const { initialValues, validation } = this.state;

        const edit = !!initialValues.id;
        const title = edit ? 'Edit triple' : 'Add triple';

        return (
            <Modal open={show} onClose={handleClose} title={title}>
                <Formik initialValues={initialValues} validationSchema={TripleSchema} onSubmit={this.handleSubmit}>
                    {({ values, errors, touched, handleChange, handleBlur, handleSubmit, isSubmitting, setValues }) => {
                        const subjectSelectable =
                            (type === 'metadata-model' && values.subjectType === 'record') ||
                            values.subjectType === 'internal' ||
                            values.subjectType === 'external';
                        let subjectOptions = subjectSelectable ? this.getOptions(values.subjectType) : [];

                        const objectSelectable =
                            (type === 'metadata-model' && values.objectType === 'record') ||
                            values.objectType === 'internal' ||
                            values.objectType === 'external' ||
                            values.objectType === 'value' ||
                            values.objectType === 'literal' ||
                            values.objectType === 'children' ||
                            values.objectType === 'parents';
                        let objectOptions = objectSelectable ? this.getOptions(values.objectType) : [];

                        if (type === 'data-model' && module && values.objectType === 'value' && module.repeated) {
                            objectOptions = objectOptions.filter(option => {
                                return option.repeated;
                            });
                        }

                        if (type === 'data-model' && module && values.objectType === 'internal' && !module.repeated) {
                            objectOptions = objectOptions.filter(option => {
                                return option.repeated === false;
                            });
                        }

                        if (type === 'data-model' && module && values.subjectType === 'internal' && !module.repeated) {
                            subjectOptions = subjectOptions.filter(option => {
                                return option.repeated === false;
                            });
                        }

                        return (
                            <Form>
                                <FormItem label="Subject">
                                    <Stack direction="row" spacing={2} sx={{ width: '100%' }}>
                                        <Box sx={{ width: 150 }}>
                                            <FormItem label="Type">
                                                <Field
                                                    component={Select}
                                                    options={tripleTypes[type].subject}
                                                    serverError={validation}
                                                    name="subjectType"
                                                    width="small"
                                                    sx={{ width: '100%' }}
                                                />
                                            </FormItem>
                                        </Box>

                                        {subjectSelectable && (
                                            <Box sx={{ flex: 1 }}>
                                                <FormItem label="Node">
                                                    <Field
                                                        component={Select}
                                                        options={subjectOptions}
                                                        serverError={validation}
                                                        name="subjectValue"
                                                        width="small"
                                                        sx={{ width: '100%' }}
                                                    />
                                                </FormItem>
                                            </Box>
                                        )}
                                    </Stack>
                                </FormItem>

                                <FormItem label="Predicate">
                                    <FormItem label="URI">
                                        <Box sx={{ width: '100%' }}>
                                            <Field
                                                component={UriInput}
                                                options={prefixes}
                                                serverError={validation}
                                                name="predicateValue"
                                                fullWidth
                                            />
                                        </Box>
                                    </FormItem>
                                </FormItem>

                                <FormItem label="Object">
                                    <Stack direction="row" spacing={2} sx={{ width: '100%' }}>
                                        <Box sx={{ width: 150 }}>
                                            <FormItem label="Type">
                                                <Field
                                                    component={Select}
                                                    options={tripleTypes[type].object}
                                                    serverError={validation}
                                                    name="objectType"
                                                    width="small"
                                                    sx={{ width: '100%' }}
                                                />
                                            </FormItem>
                                        </Box>

                                        {objectSelectable && (
                                            <Box sx={{ flex: 1 }}>
                                                <FormItem label="Node">
                                                    <Field
                                                        component={Select}
                                                        options={objectOptions}
                                                        serverError={validation}
                                                        name="objectValue"
                                                        width="small"
                                                        sx={{ width: '100%' }}
                                                    />
                                                </FormItem>
                                            </Box>
                                        )}
                                    </Stack>
                                </FormItem>

                                <Button
                                    type="submit"
                                    disabled={isSubmitting}
                                    variant="contained"
                                >
                                    {values.id ? 'Edit triple' : 'Add triple'}
                                </Button>
                            </Form>
                        );
                    }}
                </Formik>
            </Modal>
        );
    }
}

export const tripleTypes = {
    'metadata-model': {
        subject: [
            { value: 'internal', label: 'Internal' },
            { value: 'external', label: 'External' },
            { value: 'record', label: 'Record' },
        ],
        object: [
            { value: 'internal', label: 'Internal' },
            { value: 'external', label: 'External' },
            { value: 'record', label: 'Record' },
            { value: 'literal', label: 'Literal' },
            { value: 'value', label: 'Value' },
            { value: 'children', label: 'Children' },
            { value: 'parents', label: 'Parents' },
        ],
    },
    'data-model': {
        subject: [
            { value: 'internal', label: 'Internal' },
            { value: 'external', label: 'External' },
            { value: 'record', label: 'Record' },
        ],
        object: [
            { value: 'internal', label: 'Internal' },
            { value: 'external', label: 'External' },
            { value: 'record', label: 'Record' },
            { value: 'literal', label: 'Literal' },
            { value: 'value', label: 'Value' },
        ],
    },
};

const defaultData = {
    'metadata-model': {
        subjectType: 'internal',
        subjectValue: '',
        predicateValue: '',
        objectType: 'internal',
        objectValue: '',
    },
    'data-model': {
        subjectType: 'internal',
        subjectValue: '',
        predicateValue: '',
        objectType: 'internal',
        objectValue: '',
    },
};

const TripleSchema = Yup.object().shape({
    subjectType: Yup.string().oneOf(['internal', 'external', 'record'], 'Please select a subject type'),
    subjectValue: Yup.string().when('subjectType', {
        is: subjectType => subjectType === 'internal' || subjectType === 'external',
        then: Yup.string().required('Please select a node'),
    }),
    predicateValue: Yup.string().required('Please enter a predicate').url('Please enter a valid predicate'),
    objectType: Yup.string().oneOf(['internal', 'external', 'record', 'literal', 'value', 'children', 'parents'], 'Please select an object type'),
    objectValue: Yup.string().when('objectType', {
        is: objectType =>
            objectType === 'internal' ||
            objectType === 'external' ||
            objectType === 'value' ||
            objectType === 'literal' ||
            objectType === 'children' ||
            objectType === 'parents',
        then: Yup.string().required('Please select a node'),
    }),
});

export default withNotifications(TripleModal);
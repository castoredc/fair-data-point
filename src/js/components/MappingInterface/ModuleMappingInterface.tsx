import React, { Component } from 'react';
import { Field, Form, Formik } from 'formik';
import * as Yup from 'yup';
import FormItem from '../Form/FormItem';
import Button from '@mui/material/Button';
import { apiClient } from '../../network';
import Choice from 'components/Input/Formik/Choice';
import Select from 'components/Input/Formik/Select';
import { Typography } from '@mui/material';
import withNotifications, { ComponentWithNotifications } from 'components/WithNotifications';

interface ModuleMappingInterfaceProps extends ComponentWithNotifications {
    mapping: any;
    dataset: string;
    distribution: any;
    versionId: string;
    structure: any;
    onSave: () => void;
}

interface ModuleMappingInterfaceState {
    isLoading: boolean;
}

interface FormValues {
    type: string;
    element: string | null;
}

class ModuleMappingInterface extends Component<ModuleMappingInterfaceProps, ModuleMappingInterfaceState> {
    constructor(props: ModuleMappingInterfaceProps) {
        super(props);

        this.state = {
            isLoading: false,
        };
    }

    handleSubmit = (values: FormValues) => {
        const { mapping, dataset, distribution, versionId, onSave, notifications } = this.props;

        this.setState({ isLoading: true });

        apiClient
            .post(`/api/dataset/${dataset}/distribution/${distribution.slug}/contents/rdf/v/${versionId}/module`, {
                type: 'module',
                module: mapping.module.id,
                structureType: values.type,
                element: values.element,
            })
            .then(() => {
                this.setState({ isLoading: false });
                notifications.show('The mapping was successfully saved.', {
                    variant: 'success',

                });
                onSave();
            })
            .catch(error => {
                this.setState({ isLoading: false });
                const message =
                    error.response && typeof error.response.data.error !== 'undefined'
                        ? error.response.data.error
                        : 'An error occurred while saving the mapping';
                notifications.show(message, { variant: 'error' });
            });
    };

    render() {
        const { structure, mapping } = this.props;
        const { isLoading } = this.state;

        const initialValues: FormValues = {
            type: mapping?.element ? mapping.element.structureType : 'report',
            element: mapping?.element ? mapping.element.id : null,
        };

        const validationSchema = Yup.object().shape({
            type: Yup.string().required('This field is required'),
            element: Yup.string().required('This field is required'),
        });

        return (
            <>
                <Typography variant="h5">
                    {`${mapping.element ? `Edit` : `Add`} mapping for ${mapping.module.displayName}`}
                </Typography>

                <Formik initialValues={initialValues} validationSchema={validationSchema} onSubmit={this.handleSubmit}
                        enableReinitialize>
                    {({ values, handleChange, setFieldValue, errors, touched }) => {
                        const structureItems = values.type !== '' && values.type !== null ? structure[values.type] : [];

                        const options = structureItems.map((item: any) => ({
                            label: item.name,
                            value: item.id,
                        }));

                        return (
                            <Form>
                                <FormItem label="Type">
                                    <Field
                                        component={Choice}
                                        options={[
                                            { value: 'report', label: 'Report' },
                                            { value: 'survey', label: 'Survey' },
                                        ]}
                                        collapse
                                        name="type"
                                        onChange={e => {
                                            handleChange(e);
                                            setFieldValue('element', '');
                                        }}
                                    />
                                </FormItem>

                                <FormItem label="Element">
                                    <Field component={Select} options={options} name="element" />
                                </FormItem>

                                <Button
                                    type="submit"
                                    disabled={isLoading}
                                    variant="contained"
                                >
                                    {mapping && mapping.element ? 'Edit mapping' : 'Add mapping'}
                                </Button>
                            </Form>
                        );
                    }}
                </Formik>
            </>
        );
    }
}

export default withNotifications(ModuleMappingInterface);
import React, { Component } from 'react';
import { Field, Form, Formik } from 'formik';
import * as Yup from 'yup';
import FormItem from '../Form/FormItem';
import { Button, Heading } from '@castoredc/matter';
import { toast } from 'react-toastify';
import ToastItem from 'components/ToastItem';
import { apiClient } from '../../network';
import Choice from 'components/Input/Formik/Choice';
import Select from 'components/Input/Formik/Select';

interface ModuleMappingInterfaceProps {
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

export default class ModuleMappingInterface extends Component<ModuleMappingInterfaceProps, ModuleMappingInterfaceState> {
    constructor(props: ModuleMappingInterfaceProps) {
        super(props);

        this.state = {
            isLoading: false,
        };
    }

    handleSubmit = (values: FormValues) => {
        const { mapping, dataset, distribution, versionId, onSave } = this.props;

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
                toast.success(<ToastItem type="success" title="The mapping was successfully saved." />, {
                    position: 'top-right',
                });
                onSave();
            })
            .catch(error => {
                this.setState({ isLoading: false });
                const message =
                    error.response && typeof error.response.data.error !== 'undefined'
                        ? error.response.data.error
                        : 'An error occurred while saving the mapping';
                toast.error(<ToastItem type="error" title={message} />);
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
                <Heading type="Panel">{`${mapping.element ? `Edit` : `Add`} mapping for ${mapping.module.displayName}`}</Heading>

                <Formik
                    initialValues={initialValues}
                    validationSchema={validationSchema}
                    onSubmit={this.handleSubmit}
                    enableReinitialize
                >
                    {({ values, handleChange, setFieldValue, errors, touched }) => {
                        const structureItems = values.type !== '' && values.type !== null ? structure[values.type] : [];

                        const options = structureItems.map((item: any) => ({
                            label: item.name,
                            value: item.id,
                        }));

                        return <Form>
                            <FormItem label="Type">
                                <Field
                                    component={Choice}
                                    options={[
                                        { value: 'report', labelText: 'Report' },
                                        { value: 'survey', labelText: 'Survey' },
                                    ]}
                                    collapse
                                    name="type"
                                    onChange={(e) => {
                                        handleChange(e);
                                        setFieldValue('element', '');
                                    }}
                                />
                            </FormItem>

                            <FormItem label="Element">
                                <Field
                                    component={Select}
                                    options={options}
                                    name="element"
                                    menuPosition="fixed"
                                />
                            </FormItem>

                            <Button type="submit" disabled={isLoading}>
                                {mapping && mapping.element ? 'Edit mapping' : 'Add mapping'}
                            </Button>
                        </Form>
                    }}
                </Formik>
            </>
        );
    }
}
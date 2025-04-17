import React, { Component } from 'react';

import Button from '@mui/material/Button';
import LoadingOverlay from 'components/LoadingOverlay';
import FormItem from './../FormItem';
import { mergeData } from '../../../util';
import * as H from 'history';
import { Field, Form, Formik } from 'formik';
import Input from 'components/Input/Formik/Input';
import SingleChoice from 'components/Input/Formik/SingleChoice';
import * as Yup from 'yup';
import { apiClient } from 'src/js/network';
import Select from 'components/Input/Formik/Select';
import Stack from '@mui/material/Stack';
import withNotifications, { ComponentWithNotifications } from 'components/WithNotifications';
import AddIcon from '@mui/icons-material/Add';

interface CatalogFormProps extends ComponentWithNotifications {
    catalog?: any;
    history: H.History;
}

interface CatalogFormState {
    initialValues: any;
    update: boolean;
    validation?: any;
    metadataModels: any;
    hasLoadedMetadataModels: boolean;
}

class CatalogForm extends Component<CatalogFormProps, CatalogFormState> {
    constructor(props) {
        super(props);

        this.state = {
            initialValues: props.catalog ? mergeData(defaultData, props.catalog) : defaultData,
            validation: {},
            update: !!props.catalog,
            metadataModels: [],
            hasLoadedMetadataModels: false,
        };
    }

    componentDidMount() {
        this.getMetadataModels();
    }

    getMetadataModels = () => {
        const { notifications } = this.props;

        apiClient
            .get('/api/metadata-model/my')
            .then(response => {
                this.setState({
                    metadataModels: response.data.results.map(metadataModel => {
                        const versions = metadataModel.versions.map(version => {
                            return { value: version.id, label: version.version };
                        });

                        return {
                            label: metadataModel.title,
                            value: metadataModel.id,
                            versions: versions,
                        };
                    }),
                    hasLoadedMetadataModels: true,
                });
            })
            .catch(() => {
                notifications.show('An error occurred', { variant: 'error' });
            });
    };

    handleSubmit = (values, { setSubmitting }) => {
        const { catalog, history, notifications } = this.props;

        apiClient
            .post('/api/catalog' + (catalog ? '/' + catalog.slug : ''), values)
            .then(response => {
                setSubmitting(false);

                if (catalog) {
                    history.push('/dashboard/catalogs/' + values.slug);
                    notifications.show('The catalog details are saved successfully', {
                        variant: 'success',

                    });
                } else {
                    history.push('/dashboard/catalogs/' + response.data.slug + '/metadata');
                }
            })
            .catch(error => {
                setSubmitting(false);

                if (error.response && error.response.status === 400) {
                    this.setState({
                        validation: error.response.data.fields,
                    });
                } else {
                    notifications.show('An error occurred', { variant: 'error' });
                }
            });
    };

    render() {
        const { initialValues, validation, hasLoadedMetadataModels, metadataModels } = this.state;
        const { catalog } = this.props;

        if (!hasLoadedMetadataModels) {
            return <LoadingOverlay accessibleLabel="Loading catalog" />;
        }

        return (
            <Formik initialValues={initialValues} onSubmit={this.handleSubmit} validationSchema={CatalogSchema}>
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
                            <div className="FormContent">
                                <FormItem label="Slug"
                                          tooltip="The unique identifying part of a web address, typically at the end of the URL">
                                    <Field component={Input} name="slug" serverError={validation} />
                                </FormItem>

                                <FormItem label="Default metadata model">
                                    <Field
                                        component={Select}
                                        options={metadataModels}
                                        name="defaultMetadataModel"


                                        details="Please select which semantic metadata model you want to use as default"
                                    />
                                </FormItem>

                                <FormItem>
                                    <Field
                                        component={SingleChoice}
                                        labelText="Accepts submissions"
                                        name="acceptSubmissions"
                                        details="When selected, others will be able to add their study to this catalog"
                                    />
                                </FormItem>
                            </div>

                            {catalog ? (
                                <div className="FormButtons">
                                    <Stack direction="row" sx={{ justifyContent: 'space-between' }}>
                                        <Button
                                            disabled={isSubmitting}
                                            type="submit"
                                            variant="contained"
                                        >
                                            Update catalog
                                        </Button>
                                    </Stack>
                                </div>
                            ) : (
                                <footer>
                                    <Button
                                        disabled={isSubmitting}
                                        type="submit"
                                        variant="contained"
                                        startIcon={<AddIcon />}
                                    >
                                        Add catalog
                                    </Button>
                                </footer>
                            )}
                        </Form>
                    );
                }}
            </Formik>
        );
    }
}

export const defaultData = {
    slug: '',
    defaultMetadataModel: '',
    acceptSubmissions: false,
    submissionAccessesData: false,
};

const CatalogSchema = Yup.object().shape({
    slug: Yup.string().required('Please enter a slug'),
    defaultMetadataModel: Yup.string().required('Please select a metadata model'),
    acceptSubmissions: Yup.boolean().required('Please enter if this catalog accepts submissions'),
    submissionAccessesData: Yup.boolean().when('acceptSubmissions', {
        is: true,
        then: Yup.boolean().required('Please enter if the submission accesses data in the process'),
    }),
});

export default withNotifications(CatalogForm);
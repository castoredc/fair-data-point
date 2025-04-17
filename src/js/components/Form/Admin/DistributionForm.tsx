import React, { Component } from 'react';

import LoadingOverlay from 'components/LoadingOverlay';
import FormItem from './../FormItem';
import { mergeData } from '../../../util';
import { Field, Form, Formik } from 'formik';
import Choice from 'components/Input/Formik/Choice';
import Select from 'components/Input/Formik/Select';
import Input from 'components/Input/Formik/Input';
import * as Yup from 'yup';
import SingleChoice from 'components/Input/Formik/SingleChoice';
import * as H from 'history';
import { apiClient } from 'src/js/network';
import Stack from '@mui/material/Stack';
import Button from '@mui/material/Button';
import { Alert, Card, CardContent, Typography } from '@mui/material';
import withNotifications, { ComponentWithNotifications } from 'components/WithNotifications';

interface DistributionFormProps extends ComponentWithNotifications {
    distribution?: any;
    dataset?: any;
    history: H.History;
    mainUrl: string;
}

interface DistributionFormState {
    initialValues: any;
    languages: any;
    licenses: any;
    dataModels: any;
    metadataModels: any;
    distribution: any;
    hasLoadedLanguages: any;
    hasLoadedLicenses: any;
    hasLoadedDataModels: any;
    hasLoadedMetadataModels: any;
    update: boolean;
    validation?: any;
}

class DistributionForm extends Component<DistributionFormProps, DistributionFormState> {
    constructor(props) {
        super(props);

        let data = props.distribution ? mergeData(defaultData, props.distribution) : defaultData;
        const apiUser = props.distribution ? props.distribution.hasApiUser : false;
        data = {
            ...data,
            useApiUser: apiUser,
            apiUserEncrypted: apiUser,
        };

        if (typeof data.dataModel === 'object' && data.dataModel !== '') {
            data = {
                ...data,
                dataModel: data.dataModel.dataSpecification,
                dataModelVersion: data.dataModel.id,
            };
        }

        this.state = {
            initialValues: data,
            languages: [],
            licenses: [],
            dataModels: [],
            hasLoadedLanguages: false,
            hasLoadedLicenses: false,
            hasLoadedDataModels: false,
            distribution: props.distribution ? props.distribution : null,
            update: !!props.distribution,
            metadataModels: [],
            hasLoadedMetadataModels: false,
        };
    }

    componentDidMount() {
        this.getLanguages();
        this.getLicenses();
        this.getDataModels();
        this.getMetadataModels();
    }

    getLanguages = () => {
        const { notifications } = this.props;

        apiClient
            .get('/api/languages')
            .then(response => {
                this.setState({
                    languages: response.data,
                    hasLoadedLanguages: true,
                });
            })
            .catch(error => {
                notifications.show('An error occurred', { variant: 'error' });
            });
    };

    getLicenses = () => {
        const { notifications } = this.props;

        apiClient
            .get('/api/licenses')
            .then(response => {
                this.setState({
                    licenses: response.data,
                    hasLoadedLicenses: true,
                });
            })
            .catch(() => {
                notifications.show('An error occurred', { variant: 'error' });
            });
    };

    getDataModels = () => {
        const { notifications } = this.props;

        apiClient
            .get('/api/data-model/my')
            .then(response => {
                this.setState({
                    dataModels: response.data.map(dataModel => {
                        const versions = dataModel.versions.map(version => {
                            return { value: version.id, label: version.version };
                        });

                        return {
                            label: dataModel.title,
                            value: dataModel.id,
                            versions: versions,
                        };
                    }),
                    hasLoadedDataModels: true,
                });
            })
            .catch(() => {
                notifications.show('An error occurred', { variant: 'error' });
            });
    };

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
        const { dataset, distribution, mainUrl, history, notifications } = this.props;

        const url = '/api/dataset/' + dataset + '/distribution' + (distribution ? '/' + distribution.slug : '');

        apiClient
            .post(url, values)
            .then(response => {
                this.setState({
                    distribution: response.data,
                });

                if (distribution) {
                    history.push(mainUrl + '/distributions/' + values.slug);
                    notifications.show('The distribution details are saved successfully', {
                        variant: 'success',

                    });
                } else {
                    history.push(mainUrl + '/distributions/' + response.data.slug + '/metadata');
                }

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

    render() {
        const {
            initialValues,
            licenses,
            dataModels,
            metadataModels,
            distribution,
            hasLoadedLanguages,
            hasLoadedLicenses,
            hasLoadedDataModels,
            hasLoadedMetadataModels,
        } = this.state;

        if (!hasLoadedLanguages || !hasLoadedLicenses || !hasLoadedDataModels || !hasLoadedMetadataModels) {
            return <LoadingOverlay accessibleLabel="Loading distribution" />;
        }

        return (
            <Formik initialValues={initialValues} onSubmit={this.handleSubmit}>
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
                    const currentDataModel = dataModels.find(({ value }) => value === values.dataModel);

                    return (
                        <Form>
                            <Card variant="outlined" sx={{ mb: 2 }}>
                                <CardContent>
                                    <div>
                                        <FormItem label="Distribution type">
                                            <Field component={Choice} options={distributionTypes} name="type" />
                                        </FormItem>

                                        <FormItem
                                            label="Slug"
                                            tooltip="The unique identifying part of a web address, typically at the end of the URL"
                                        >
                                            <Field component={Input} name="slug" />
                                        </FormItem>

                                        <FormItem label="Default metadata model">
                                            <Field
                                                component={Select}
                                                options={metadataModels}
                                                name="defaultMetadataModel"


                                                details="Please select which semantic metadata model you want to use as default"
                                            />
                                        </FormItem>

                                        <FormItem label="License"
                                                  details="The reference to the usage license of the distribution">
                                            <Field component={Select} options={licenses} name="license" />
                                        </FormItem>

                                        <FormItem
                                            label="Publish distribution metadata"
                                            details="When enabled, the metadata of this distribution will be publicly accessible on the internet"
                                        >
                                            <Field
                                                component={Choice}
                                                options={[
                                                    {
                                                        label: 'Yes',
                                                        value: true,
                                                    },
                                                    {
                                                        label: 'No',
                                                        value: false,
                                                    },
                                                ]}
                                                collapse
                                                name="published"
                                            />
                                        </FormItem>


                                    </div>
                                </CardContent>
                            </Card>
                            {values.type === 'csv' && (
                                <Card variant="outlined" sx={{ mb: 2 }}>
                                    <CardContent>
                                        <Typography variant="h5" component="div">
                                            CSV Distribution
                                        </Typography>

                                        <div>
                                            <FormItem>
                                                <Field
                                                    component={SingleChoice}
                                                    label="Include all data"
                                                    name="includeAllData"
                                                    details="Rather than using a pre-defined data dictionary, all data from the study will be included in the distribution"
                                                />
                                            </FormItem>
                                        </div>
                                    </CardContent>
                                </Card>
                            )}

                            {values.type === 'rdf' && (
                                <Card variant="outlined" sx={{ mb: 2 }}>
                                    <CardContent>
                                        <Typography variant="h5" component="div">
                                            RDF Distribution
                                        </Typography>

                                        <div>
                                            <Stack direction="row">
                                                <FormItem label="Data model">
                                                    <Field
                                                        component={Select}
                                                        options={dataModels}
                                                        name="dataModel"
                                                        details="The pre-defined semantic data model describing all relations between data elements"
                                                    />
                                                </FormItem>
                                                <FormItem label="Data model version">
                                                    {currentDataModel && (
                                                        <Field
                                                            component={Select}
                                                            options={currentDataModel.versions}
                                                            name="dataModelVersion"
                                                            // value={currentDataModel.versions.filter(({value}) => value === data.dataModelVersion)}
                                                        />
                                                    )}
                                                </FormItem>
                                            </Stack>
                                        </div>
                                    </CardContent>
                                </Card>
                            )}

                            <Card variant="outlined" sx={{ mb: 2 }}>
                                <CardContent>
                                    <Typography variant="h5" component="div">
                                        Data
                                    </Typography>

                                    <div>
                                        <FormItem
                                            label="Publish distribution data"
                                            details="When enabled, the data of this distribution will be publicly accessible on the internet"
                                        >
                                            <Field
                                                component={Choice}
                                                options={[
                                                    {
                                                        label: 'Yes',
                                                        value: true,
                                                    },
                                                    {
                                                        label: 'No',
                                                        value: false,
                                                    },
                                                ]}
                                                collapse
                                                name="public"
                                            />
                                        </FormItem>

                                        {values.public === true && (
                                            <Alert severity="info">
                                                Please note that this enables data access to everyone,
                                                without
                                                any access control.
                                            </Alert>
                                        )}

                                        <FormItem
                                            label="Cache distribution data"
                                            details="When enabled, the data of this distribution will be cached to enable querying and faster downloading"
                                        >
                                            <Field
                                                component={Choice}
                                                options={[
                                                    {
                                                        label: 'Yes',
                                                        value: true,
                                                    },
                                                    {
                                                        label: 'No',
                                                        value: false,
                                                    },
                                                ]}
                                                collapse
                                                name="cached"
                                            />
                                        </FormItem>
                                    </div>
                                </CardContent>
                            </Card>

                            <Card variant="outlined" sx={{ mb: 2 }}>
                                <CardContent>
                                    <Typography variant="h5" component="div">
                                        Castor EDC API Credentials
                                    </Typography>

                                    <div>

                                        <FormItem>
                                            <Field
                                                component={SingleChoice}
                                                label="Use API User"
                                                name="useApiUser"
                                                details="An external user (which should be added to the study) will be used to access the data, required for cached distributions"
                                            />
                                        </FormItem>

                                        {values.useApiUser && (
                                            <div>
                                                {values.apiUserEncrypted ? (
                                                    <div>
                                                        <p>The API Credentials for this distribution are
                                                            encrypted.</p>

                                                        <Button color="error"
                                                                onClick={() => setFieldValue('apiUserEncrypted', false)}>
                                                            Change API Credentials
                                                        </Button>
                                                    </div>
                                                ) : (
                                                    <div>
                                                        <FormItem label="Email address">
                                                            <Field component={Input} name="apiUser" />
                                                        </FormItem>

                                                        <Stack direction="row">
                                                            <FormItem label="Client ID">
                                                                <Field component={Input} name="clientId" />
                                                            </FormItem>

                                                            <FormItem label="Client Secret">
                                                                <Field component={Input}
                                                                       name="clientSecret" />
                                                            </FormItem>
                                                        </Stack>
                                                    </div>
                                                )}
                                            </div>
                                        )}
                                    </div>
                                </CardContent>
                            </Card>

                            <div>
                                <Stack direction="row" sx={{ justifyContent: 'space-between' }}>
                                    {distribution ? (
                                        <Button
                                            disabled={isSubmitting}
                                            type="submit"
                                            variant="contained"
                                        >
                                            Update distribution
                                        </Button>
                                    ) : (
                                        <Button
                                            disabled={isSubmitting}
                                            type="submit"
                                            variant="contained"
                                        >
                                            Add distribution
                                        </Button>
                                    )}
                                </Stack>
                            </div>
                        </Form>
                    );
                }}
            </Formik>
        );
    }
}

export const distributionTypes = [
    {
        value: 'csv',
        label: 'CSV Distribution',
        details: 'CSV distributions contain tabulated data, where every column represents a variable collected in your study',
    },
    {
        value: 'rdf',
        label: 'RDF Distribution',
        details: 'RDF distributions contain linked data, where the data is modeled using a pre-defined semantic data model',
    },
];

export const defaultData = {
    type: '',
    slug: '',
    defaultMetadataModel: '',
    accessRights: null,
    includeAllData: false,
    dataModel: '',
    dataModelVersion: '',
    license: null,
    useApiUser: false,
    apiUserEncrypted: false,
    apiUser: '',
    clientId: '',
    clientSecret: '',
    published: false,
    cached: false,
    public: false,
};

const DistributionSchema = Yup.object().shape({
    type: Yup.string().oneOf(['csv', 'rdf']).required('Please select a distribution type'),
    slug: Yup.string().required('Please select a slug'),
    defaultMetadataModel: Yup.string().required('Please select a metadata model'),
    includeAllData: Yup.boolean()
        .nullable()
        .when('type', {
            is: 'csv',
            then: Yup.boolean().required('Please select if all data should be included'),
        }),
    dataModel: Yup.string().when('type', {
        is: 'rdf',
        then: Yup.string().required('Please select a data model'),
    }),
    dataModelVersion: Yup.string().when('type', {
        is: 'rdf',
        then: Yup.string().required('Please select a data model version'),
    }),
    license: Yup.string().required('Please select a license'),
    useApiUser: Yup.boolean().required(),
    apiUser: Yup.string().when('useApiUser', {
        is: true,
        then: Yup.string().required('Please enter an email address'),
    }),
    clientId: Yup.string().when('useApiUser', {
        is: true,
        then: Yup.string().required('Please enter a client ID'),
    }),
    clientSecret: Yup.string().when('useApiUser', {
        is: true,
        then: Yup.string().required('Please enter a client secret'),
    }),
    published: Yup.boolean().required('Please select if this distribution should be published'),
    cached: Yup.boolean().required('Please select if this distribution should be cached'),
    public: Yup.boolean().required('Please select if the data in this distribution should be publicly available'),
});

export default withNotifications(DistributionForm);
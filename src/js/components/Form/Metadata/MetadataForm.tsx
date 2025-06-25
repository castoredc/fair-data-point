import React, { Component } from 'react';
import Button from '@mui/material/Button';
import AddIcon from '@mui/icons-material/Add';
import LoadingOverlay from 'components/LoadingOverlay';
import MetadataVersionModal from '../../../modals/MetadataVersionModal';
import { Form, Formik } from 'formik';
import { apiClient } from 'src/js/network';
import { RenderedMetadataFormType } from 'types/RenderedMetadataFormType';
import RenderedForm from 'components/Form/Metadata/RenderedForm';
import { DataSpecificationOptionGroupType } from 'types/DataSpecificationOptionGroupType';
import { getInitialValues, getSchema } from 'utils/MetadataFormHelper';
import Stack from '@mui/material/Stack';
import withNotifications, { ComponentWithNotifications } from 'components/WithNotifications';
import NoResults from 'components/NoResults';

interface MetadataFormProps extends ComponentWithNotifications {
    object: any;
    type: any;
    onCreate: () => void;
    onSave: () => void;
};

type MetadataFormState = {
    currentVersion: any;
    validation: any;
    isLoading: boolean;
    showModal: boolean;
    languages: any;
    licenses: any;
    countries: any;
    metadataModels: any;
    forms?: RenderedMetadataFormType[];
    optionGroups: DataSpecificationOptionGroupType[];
};

class MetadataForm extends Component<MetadataFormProps, MetadataFormState> {
    constructor(props) {
        super(props);

        this.state = {
            currentVersion: props.object.hasMetadata ? props.object.metadata.version : null,
            validation: {},
            isLoading: false,
            showModal: false,
            languages: [],
            licenses: [],
            countries: [],
            metadataModels: undefined,
            forms: undefined,
            optionGroups: [],
        };
    }

    componentDidMount() {
        const { object } = this.props;
        const hasMetadata = !(
            !object.hasMetadata ||
            (object.hasMetadata && (object.metadata.model === null || object.metadata.modelVersion === null))
        );

        this.getMetadataModels();
        this.getLanguages();
        this.getLicenses();
        this.getCountries();

        if (hasMetadata) {
            this.getForms();
            this.getOptionGroups();
        }
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
                });
            })
            .catch(() => {
                notifications.show('An error occurred', { variant: 'error' });
            });
    };

    getForms = () => {
        const { object, notifications } = this.props;

        apiClient
            .get('/api/metadata/form/' + object.metadata.id)
            .then(response => {
                this.setState({
                    forms: response.data,
                });
            })
            .catch(() => {
                notifications.show('An error occurred', { variant: 'error' });
            });
    };

    getOptionGroups = () => {
        const { object, notifications } = this.props;

        this.setState({ isLoading: true });

        apiClient
            .get('/api/metadata-model/' + object.metadata.model + '/v/' + object.metadata.modelVersion + '/option-group')
            .then(response => {
                this.setState({
                    optionGroups: response.data,
                });
            })
            .catch(error => {
                const message =
                    error.response && typeof error.response.data.error !== 'undefined'
                        ? error.response.data.error
                        : 'An error occurred while loading the option groups';
                notifications.show(message, { variant: 'error' });
            });
    };

    getLanguages = () => {
        const { notifications } = this.props;

        apiClient
            .get('/api/languages')
            .then(response => {
                this.setState({
                    languages: response.data,
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
                });
            })
            .catch(() => {
                notifications.show('An error occurred', { variant: 'error' });
            });
    };

    getCountries = () => {
        const { notifications } = this.props;

        apiClient
            .get('/api/countries')
            .then(response => {
                this.setState({
                    countries: response.data,
                });
            })
            .catch(error => {
                if (error.response && typeof error.response.data.error !== 'undefined') {
                    notifications.show(error.response.data.error, { variant: 'error' });
                } else {
                    notifications.show('An error occurred', { variant: 'error' });
                }
            });
    };

    closeModal = () => {
        this.setState({ showModal: false });
    };

    showModal = () => {
        this.setState({ showModal: true });
    };

    handleVersionUpdate = versionType => {
        const { onCreate } = this.props;

        this.closeModal();
        onCreate();
    };

    handleSubmit = (values, { setSubmitting }) => {
        const { object, type, onSave, notifications } = this.props;

        this.setState({
            isLoading: true,
        });

        apiClient
            .post('/api/metadata/form/' + object.metadata.id, values)
            .then(response => {
                this.setState({ isLoading: false });

                setSubmitting(false);

                notifications.show('The metadata are saved successfully', {
                    variant: 'success',

                });

                onSave();
            })
            .catch(error => {
                if (error.response && error.response.status === 400 && error.response.data.fields) {
                    this.setState({
                        validation: error.response.data.fields,
                    });
                } else if (error.response) {
                    notifications.show(error.response.data.error, { variant: 'error' });
                } else {
                    notifications.show('An error occurred', { variant: 'error' });
                }

                this.setState({ isLoading: false });

                setSubmitting(false);
            });
    };

    render() {
        const {
            validation,
            languages,
            licenses,
            countries,
            currentVersion,
            showModal,
            metadataModels,
            forms,
            optionGroups,
        } = this.state;
        const { type, object } = this.props;

        if (metadataModels === undefined) {
            return <LoadingOverlay accessibleLabel="Loading metadata models" />;
        }

        if (!object.hasMetadata || (object.hasMetadata && (object.metadata.model === null || object.metadata.modelVersion === null))) {
            return (
                <>
                    <MetadataVersionModal
                        open={showModal}
                        currentVersion={currentVersion}
                        defaultMetadataModel={object.defaultMetadataModel}
                        onClose={this.closeModal}
                        handleSave={this.handleVersionUpdate}
                        metadataModels={metadataModels}
                        type={type}
                        objectId={object.id}
                    />
                    <NoResults>
                        This {type} does not have any metadata yet.
                        <br />
                        <br />
                        <Button
                            startIcon={<AddIcon />}
                            onClick={() => this.showModal()}
                            variant="contained"
                        >
                            Add metadata
                        </Button>
                    </NoResults>
                </>
            );
        }

        if (forms === undefined) {
            return <LoadingOverlay accessibleLabel="Loading forms" />;
        }

        const initialValues = getInitialValues(forms);
        const schema = getSchema(forms);

        return (
            <>
                <MetadataVersionModal
                    open={showModal}
                    currentVersion={currentVersion}
                    defaultMetadataModel={object.defaultMetadataModel}
                    onClose={this.closeModal}
                    handleSave={this.handleVersionUpdate}
                    metadataModels={metadataModels}
                    type={type}
                    objectId={object.id}
                />

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
                                <div>
                                    {forms.map(form => (
                                        <RenderedForm
                                            key={form.id}
                                            form={form}
                                            validation={validation}
                                            optionGroups={optionGroups}
                                            languages={languages}
                                            licenses={licenses}
                                            countries={countries}
                                        />
                                    ))}
                                    <Stack direction="row" sx={{ justifyContent: 'space-between', mt: 2 }}>
                                        <Button
                                            variant="outlined"
                                            startIcon={<AddIcon />}
                                            onClick={() => this.showModal()}
                                        >
                                            New metadata version
                                        </Button>
                                        <Button type="submit" disabled={isSubmitting} variant="contained">
                                            Save
                                        </Button>
                                    </Stack>
                                </div>
                            </Form>
                        );
                    }}
                </Formik>
            </>
        );
    }
}

export default withNotifications(MetadataForm);
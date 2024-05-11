import React, { Component } from 'react';
import { toast } from 'react-toastify';
import ToastItem from 'components/ToastItem';
import { Button, Card, LoadingOverlay, Stack } from '@castoredc/matter';
import MetadataVersionModal from '../../../modals/MetadataVersionModal';
import { getType, mergeData } from '../../../util';
import { Form, Formik } from 'formik';
import GeneralMetadata from 'components/Form/Metadata/FormGroups/GeneralMetadata';
import PublishersMetadata from 'components/Form/Metadata/FormGroups/PublishersMetadata';
import { apiClient } from 'src/js/network';
import PageTabs from 'components/PageTabs';
import { RenderedMetadataFormType } from 'types/RenderedMetadataFormType';
import RenderedForm from 'components/Form/Metadata/RenderedForm';
import { DataSpecificationOptionGroupType } from 'types/DataSpecificationOptionGroupType';
import { getInitialValues, getSchema } from 'utils/MetadataFormHelper';

type MetadataFormProps = {
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

export default class MetadataForm extends Component<MetadataFormProps, MetadataFormState> {
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
            metadataModels: [],
            forms: undefined,
            optionGroups: [],
        };
    }

    componentDidMount() {
        this.getMetadataModels();
        this.getForms();
        this.getOptionGroups();
        this.getLanguages();
        this.getLicenses();
        this.getCountries();
    }

    getMetadataModels = () => {
        apiClient
            .get('/api/metadata-model/my')
            .then(response => {
                this.setState({
                    metadataModels: response.data.map(metadataModel => {
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
                toast.error(<ToastItem type="error" title="An error occurred" />);
            });
    };

    getForms = () => {
        const { object } = this.props;

        apiClient
            .get('/api/metadata/form/' + object.metadata.id)
            .then(response => {
                this.setState({
                    forms: response.data
                });
            })
            .catch(() => {
                toast.error(<ToastItem type="error" title="An error occurred" />);
            });
    };

    getOptionGroups = () => {
        const { object } = this.props;
        const { type } = this.props;

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
                toast.error(<ToastItem type="error" title={message} />);
            });
    };

    getLanguages = () => {
        apiClient
            .get('/api/languages')
            .then(response => {
                this.setState({
                    languages: response.data,
                });
            })
            .catch(error => {
                toast.error(<ToastItem type="error" title="An error occurred" />);
            });
    };

    getLicenses = () => {
        apiClient
            .get('/api/licenses')
            .then(response => {
                this.setState({
                    licenses: response.data,
                });
            })
            .catch(() => {
                toast.error(<ToastItem type="error" title="An error occurred" />);
            });
    };

    getCountries = () => {
        apiClient
            .get('/api/countries')
            .then(response => {
                this.setState({
                    countries: response.data,
                });
            })
            .catch(error => {
                if (error.response && typeof error.response.data.error !== 'undefined') {
                    toast.error(<ToastItem type="error" title={error.response.data.error} />);
                } else {
                    toast.error(<ToastItem type="error" title="An error occurred" />);
                }
            });
    };

    closeModal = () => {
        this.setState({ showModal: false });
    };

    showModal = () => {
        this.setState({ showModal: true });
    };

    handleVersionUpdate = (versionType) => {
        const { onCreate } = this.props;

        this.closeModal();
        onCreate();
    };

    handleSubmit = (values, { setSubmitting }) => {
        const { object, type, onSave } = this.props;

        this.setState({
            isLoading: true,
        });

        apiClient
            .post('/api/metadata/form/' + object.metadata.id, values)
            .then(response => {
                this.setState({ isLoading: false });

                setSubmitting(false);

                toast.success(<ToastItem type="success" title="The metadata are saved successfully" />, {
                    position: 'top-right',
                });

                onSave();
            })
            .catch(error => {
                if (error.response && error.response.status === 400) {
                    this.setState({
                        validation: error.response.data.fields,
                    });
                } else {
                    toast.error(<ToastItem type="error" title="An error occurred" />);
                }

                this.setState({ isLoading: false });

                setSubmitting(false);
            });
    };

    render() {
        const { validation, languages, licenses, countries, currentVersion, showModal, metadataModels, forms, optionGroups } = this.state;
        const { type, object } = this.props;

        if(!object.hasMetadata || object.hasMetadata && (object.metadata.model === null || object.metadata.modelVersion === null)) {
            return (<>
                    <MetadataVersionModal
                        open={showModal}
                        currentVersion={currentVersion}
                        onClose={this.closeModal}
                        handleSave={this.handleVersionUpdate}
                        metadataModels={metadataModels}
                        type={type}
                        objectId={object.id}
                    />

                    <div className="NoResults">
                        This {type} does not have any metadata yet.
                        <br />
                        <br />
                        <Button icon="add" onClick={() => this.showModal()}>
                            Add metadata
                        </Button>
                    </div>
                </>
            )
        }

        if(forms === undefined) {
            return <LoadingOverlay accessibleLabel="Loading forms" />
        }

        const initialValues = getInitialValues(forms);
        const schema = getSchema(forms);

        console.log(schema);

        return (
            <>
                <MetadataVersionModal
                    open={showModal}
                    currentVersion={currentVersion}
                    onClose={this.closeModal}
                    handleSave={versionNumber => ''}
                    metadataModels={metadataModels}
                    type={type}
                    objectId={object}
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
                        console.log(errors);

                        return (
                            <Form>
                                <div className="FormContent">
                                    {forms.map((form) => {
                                        return <RenderedForm
                                            key={form.id}
                                            form={form}
                                            validation={validation}
                                            optionGroups={optionGroups}
                                            languages={languages}
                                            licenses={licenses}
                                            countries={countries}
                                        />;
                                    })}
                                </div>
                                <div className="FormButtons">
                                    <Stack distribution="trailing">
                                        <Button
                                            buttonType="primary"
                                            type="submit"
                                            disabled={isSubmitting}
                                        >
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

import React, {Component} from 'react';
import {toast} from "react-toastify";
import ToastContent from "../../ToastContent";
import axios from "axios";
import MetadataVersionModal from "../../../modals/MetadataVersionModal";
import {Button, Stack, Tabs} from "@castoredc/matter";
import {mergeData} from "../../../util";
import {Form, Formik} from "formik";
import GeneralMetadata from "components/Form/Metadata/FormGroups/GeneralMetadata";
import PublishersMetadata from "components/Form/Metadata/FormGroups/PublishersMetadata";

type MetadataFormProps = {
    object: any,
    type: any,
    onSave: () => void,
    children(validation: any, languages: any): React.ReactNode;
}

type MetadataFormState = {
    data: any,
    currentVersion: any,
    validation: any,
    isLoading: boolean,
    showModal: boolean,
    languages: any,
    licenses: any,
    countries: any,
}

export default class MetadataForm extends Component<MetadataFormProps, MetadataFormState> {
    constructor(props) {
        super(props);

        const extendedDefaultData = props.defaultData ? props.defaultData : {};

        const mergedDefaultMetadata = {
            ...defaultData,
            ...extendedDefaultData,
        };

        this.state = {
            data: props.object.hasMetadata ? mergeData(mergedDefaultMetadata, props.object.metadata) : mergedDefaultMetadata,
            currentVersion: props.object.hasMetadata ? props.object.metadata.version.metadata : null,
            validation: {},
            // isSaved: false,
            // submitDisabled: false,
            isLoading: false,
            showModal: false,
            languages: [],
            licenses: [],
            countries: [],
        };
    }

    componentDidMount() {
        this.getLanguages();
        this.getLicenses();
        this.getCountries();
    }

    getLanguages = () => {
        axios.get('/api/languages')
            .then((response) => {
                this.setState({
                    languages: response.data,
                });
            })
            .catch((error) => {
                toast.error(<ToastContent type="error" message="An error occurred"/>);
            });
    };

    getLicenses = () => {
        axios.get('/api/licenses')
            .then((response) => {
                this.setState({
                    licenses: response.data,
                });
            })
            .catch(() => {
                toast.error(<ToastContent type="error" message="An error occurred"/>);
            });
    };

    getCountries = () => {
        axios.get('/api/countries')
            .then((response) => {
                this.setState({
                    countries: response.data,
                });
            })
            .catch((error) => {
                if (error.response && typeof error.response.data.error !== "undefined") {
                    toast.error(<ToastContent type="error" message={error.response.data.error}/>);
                } else {
                    toast.error(<ToastContent type="error" message="An error occurred"/>);
                }
            });
    };

    closeModal = () => {
        this.setState({showModal: false,});
    };

    handleVersionUpdate = (versionType, setFieldValue, handleSubmit) => {
        this.closeModal();

        const {data} = this.state;

        setFieldValue('versionUpdate', versionType);

        this.setState({
            data: {
                ...data,
                versionUpdate: versionType,
            },
        }, () => {
            handleSubmit();
        });
    };

    showVersionModal = (setFieldValue, handleSubmit) => {
        const {currentVersion} = this.state;

        if (currentVersion === null) {
            this.handleVersionUpdate('major', setFieldValue, handleSubmit);
        } else {
            this.setState({showModal: true});
        }
    };

    handleSubmit = (values, {setSubmitting}) => {
        const {object, type, onSave} = this.props;

        this.setState({
            isLoading: true,
        });

        axios.post('/api/metadata/' + type + (type === 'fdp' ? '' : '/' + object.id), values)
            .then((response) => {
                this.setState({isLoading: false});

                setSubmitting(false);

                toast.success(<ToastContent type="success" message="The metadata are saved successfully"/>, {
                    position: "top-right",
                });

                onSave();
            })
            .catch((error) => {
                if (error.response && error.response.status === 400) {
                    this.setState({
                        validation: error.response.data.fields,
                    });
                } else {
                    toast.error(<ToastContent type="error" message="An error occurred"/>, {
                        position: "top-center",
                    });
                }

                this.setState({isLoading: false});

                setSubmitting(false);
            });
    };

    render() {
        const {
            data,
            validation,
            languages,
            licenses,
            countries,
            currentVersion,
            showModal,
        } = this.state;

        return <>

            <Formik
                initialValues={data}
                onSubmit={this.handleSubmit}
            >
                {({
                      values,
                      errors,
                      touched,
                      handleChange,
                      handleBlur,
                      handleSubmit,
                      isSubmitting,
                      setValues,
                      setFieldValue
                  }) => {
                    return <Form>
                        <MetadataVersionModal
                            open={showModal}
                            currentVersion={currentVersion}
                            onClose={this.closeModal}
                            handleSave={(versionType) => this.handleVersionUpdate(versionType, setFieldValue, handleSubmit)}
                        />
                        <div className="PageTabs">
                            <Tabs
                                tabs={{
                                    metadata: {
                                        title: 'Metadata',
                                        content: <>
                                            <GeneralMetadata values={values}
                                                             languages={languages} licenses={licenses}
                                                             countries={countries} validation={validation}/>
                                        </>,
                                    },
                                    publishers: {
                                        title: 'Publishers',
                                        content: <PublishersMetadata
                                            publishers={values.publishers}
                                            setValue={setFieldValue}
                                            languages={languages}
                                            licenses={licenses}
                                            countries={countries}
                                            validation={validation}
                                        />,
                                    },
                                }}
                            />
                        </div>

                        <div className="FormButtons">
                            <Stack distribution="trailing">
                                <Button buttonType="primary" onClick={() => this.showVersionModal(setFieldValue, handleSubmit)} disabled={isSubmitting}>
                                    Save
                                </Button>
                            </Stack>
                        </div>
                    </Form>
                }}
            </Formik>
        </>;
    }
}

const defaultData = {
    'title': [{
        text: '',
        language: null
    }],
    'description': [{
        text: '',
        language: null
    }],
    'language': null,
    'license': null,
    'versionUpdate': '',
    'publishers': [],
};
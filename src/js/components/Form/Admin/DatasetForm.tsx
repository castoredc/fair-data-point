import React, { Component } from 'react';

import '../Form.scss';
import { toast } from 'react-toastify';
import ToastItem from 'components/ToastItem';
import { Button, Icon, LoadingOverlay, Stack } from '@castoredc/matter';
import FormItem from './../FormItem';
import { localizedText, mergeData } from '../../../util';
import * as H from 'history';
import { Field, Form, Formik } from 'formik';
import Input from 'components/Input/Formik/Input';
import SingleChoice from 'components/Input/Formik/SingleChoice';
import * as Yup from 'yup';
import { apiClient } from 'src/js/network';
import Select from 'components/Input/Formik/Select';
import { Link } from 'react-router-dom';

interface DatasetFormProps {
    dataset?: any;
    history: H.History;
    mainUrl: string;
}

interface DatasetFormState {
    initialValues: any;
    update: boolean;
    validation?: any;
    metadataModels: any;
    hasLoadedMetadataModels: boolean;
}

export default class DatasetForm extends Component<DatasetFormProps, DatasetFormState> {
    constructor(props) {
        super(props);

        this.state = {
            initialValues: props.dataset ? mergeData(defaultData, props.dataset) : defaultData,
            validation: {},
            update: !!props.dataset,
            metadataModels: [],
            hasLoadedMetadataModels: false,
        };
    }

    componentDidMount() {
        this.getMetadataModels();
    }

    getMetadataModels = () => {
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
                toast.error(<ToastItem type="error" title="An error occurred" />);
            });
    };

    handleSubmit = (values, { setSubmitting }) => {
        const { dataset, history, mainUrl } = this.props;

        apiClient
            .post('/api/dataset/' + dataset.slug, values)
            .then(response => {
                setSubmitting(false);

                history.push(mainUrl + '/datasets/' + values.slug);
                toast.success(<ToastItem type="success" title="The dataset details are saved successfully" />, {
                    position: 'top-right',
                });
            })
            .catch(error => {
                setSubmitting(false);

                if (error.response && error.response.status === 400) {
                    this.setState({
                        validation: error.response.data.fields,
                    });
                } else {
                    toast.error(<ToastItem type="error" title="An error occurred" />);
                }
            });
    };

    render() {
        const { initialValues, validation, hasLoadedMetadataModels, metadataModels } = this.state;
        const { dataset } = this.props;

        if (!hasLoadedMetadataModels) {
            return <LoadingOverlay accessibleLabel="Loading dataset" />;
        }

        return (
            <Formik initialValues={initialValues} onSubmit={this.handleSubmit} validationSchema={DatasetSchema}>
                {({ values, errors, touched, handleChange, handleBlur, handleSubmit, isSubmitting, setValues, setFieldValue }) => {
                    return (
                        <Form>
                            <div className="FormContent">
                                {dataset.study && (
                                    <FormItem label="Study">
                                        <div className="StudyLink">
                                            <div className="StudyIcon">
                                                <Icon type="study" width="32px" height="32px" />
                                            </div>
                                            <div className="StudyDetails">
                                                <div className="StudyName">
                                                    <dl>
                                                        <dt>Title</dt>
                                                        <dd>
                                                            {dataset.study.hasMetadata ? (
                                                                localizedText(dataset.study.metadata.title, 'en')
                                                            ) : (
                                                                <span className="None">None</span>
                                                            )}
                                                        </dd>
                                                        <dt>Study name</dt>
                                                        <dd>{dataset.study.name}</dd>
                                                    </dl>
                                                </div>
                                                {/* @ts-ignore */}
                                                <Link to={`/dashboard/studies/${dataset.study.id}`}>
                                                    <Button buttonType="secondary">Open study</Button>
                                                </Link>
                                            </div>
                                        </div>
                                    </FormItem>
                                )}

                                <FormItem label="Slug" tooltip="The unique identifying part of a web address, typically at the end of the URL">
                                    <Field component={Input} name="slug" serverError={validation} />
                                </FormItem>

                                <FormItem label="Default metadata model">
                                    <Field
                                        component={Select}
                                        options={metadataModels}
                                        name="defaultMetadataModel"
                                        menuPosition="fixed"
                                        menuPlacement="auto"
                                        details="Please select which semantic metadata model you want to use as default"
                                    />
                                </FormItem>

                                <FormItem>
                                    <Field
                                        component={SingleChoice}
                                        labelText="Publish dataset"
                                        name="published"
                                        details="When selected, others will be able to view metadata of this dataset"
                                    />
                                </FormItem>
                            </div>

                            <div className="FormButtons">
                                <Stack distribution="trailing">
                                    <Button disabled={isSubmitting} type="submit">
                                        Update dataset
                                    </Button>
                                </Stack>
                            </div>
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
    published: false,
};

const DatasetSchema = Yup.object().shape({
    slug: Yup.string().required('Please enter a slug'),
    defaultMetadataModel: Yup.string().required('Please select a metadata model'),
    published: Yup.boolean().required('Please enter if the dataset is published'),
});

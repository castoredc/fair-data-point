import React, { Component } from 'react';
import { Button, LoadingOverlay, Stack } from '@castoredc/matter';
import { format } from 'date-fns';
import { toast } from 'react-toastify';
import ToastItem from 'components/ToastItem';

import FormItem from 'components/Form/FormItem';
import { Field, Form, Formik } from 'formik';
import Input from 'components/Input/Formik/Input';
import FormGroup from 'components/Form/FormGroup';
import Select from 'components/Input/Formik/Select';
import DatePicker from 'components/Input/Formik/DatePicker';
import OntologyConceptFormBlock from 'components/Input/Formik/OntologyConceptFormBlock';
import LocalizedTextInput from 'components/Input/Formik/LocalizedTextInput';
import Choice from 'components/Input/Formik/Choice';
import * as Yup from 'yup';
import { apiClient } from 'src/js/network';

type StudyDetailsFormProps = {
    studyId: string;
    onSaved: () => void;
};

type StudyDetailsFormState = {
    data: any;
    isLoading: boolean;
    languages: any;
    validation: any;
};

export default class StudyDetailsForm extends Component<StudyDetailsFormProps, StudyDetailsFormState> {
    constructor(props) {
        super(props);

        this.state = {
            data: defaultData,
            isLoading: false,
            validation: {},
            languages: [],
        };
    }

    getMetadata = () => {
        const { data } = this.state;

        this.setState({
            isLoading: true,
        });

        apiClient
            .get('/api/study/' + this.props.studyId)
            .then(response => {
                let metadata = response.data.metadata;

                if (metadata != null) {
                    if (metadata.estimatedStudyStartDate !== '' && metadata.estimatedStudyStartDate !== null) {
                        metadata.estimatedStudyStartDate = new Date(metadata.estimatedStudyStartDate);
                    }
                    if (metadata.estimatedStudyCompletionDate !== '' && metadata.estimatedStudyCompletionDate !== null) {
                        metadata.estimatedStudyCompletionDate = new Date(metadata.estimatedStudyCompletionDate);
                    }
                }

                this.setState({
                    data: { ...data, ...metadata },
                    isLoading: false,
                });
            })
            .catch(error => {
                this.setState({
                    isLoading: false,
                });

                if (error.response && typeof error.response.data.error !== 'undefined') {
                    toast.error(<ToastItem type="error" title={error.response.data.error} />);
                } else {
                    toast.error(<ToastItem type="error" title="An error occurred" />);
                }
            });
    };

    componentDidMount() {
        this.getMetadata();
        this.getLanguages();
    }

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

    handleSubmit = (values, { setSubmitting }) => {
        const { studyId, onSaved } = this.props;
        const { data } = this.state;

        apiClient
            .post('/api/study/' + studyId + '/metadata' + (data.id ? '/' + data.id : ''), {
                briefName: values.briefName,
                scientificName: values.scientificName,
                briefSummary: values.briefSummary,
                type: values.studyType,
                conditions: values.conditions,
                intervention: values.intervention,
                estimatedEnrollment: values.estimatedEnrollment,
                estimatedStudyStartDate: values.estimatedStudyStartDate ? format(values.estimatedStudyStartDate, 'yyyy-MM-dd') : null,
                estimatedStudyCompletionDate: values.estimatedStudyCompletionDate ? format(values.estimatedStudyCompletionDate, 'yyyy-MM-dd') : null,
                summary: values.summary,
                recruitmentStatus: values.recruitmentStatus,
                methodType: values.methodType,
                keywords: values.keywords,
            })
            .then(response => {
                this.setState({
                    isLoading: false,
                });

                toast.success(<ToastItem type="success" title="The study details are saved successfully" />, {
                    position: 'top-right',
                });

                setSubmitting(false);
                onSaved();
            })
            .catch(error => {
                if (error.response && error.response.status === 400) {
                    this.setState({
                        validation: error.response.data.fields,
                    });
                } else {
                    toast.error(<ToastItem type="error" title="An error occurred" />);
                }
                this.setState({
                    isLoading: false,
                });

                setSubmitting(false);
            });
    };

    render() {
        const { isLoading, data, languages, validation } = this.state;

        if (isLoading) {
            return <LoadingOverlay accessibleLabel="Loading study information" />;
        }

        return (
            <>
                <Formik initialValues={data} onSubmit={this.handleSubmit} validationSchema={StudyDetailsSchema}>
                    {({ values, errors, touched, handleChange, handleBlur, handleSubmit, isSubmitting, setValues, setFieldValue }) => {
                        return (
                            <Form>
                                <div className="FormContent">
                                    <FormGroup label="Study Identification">
                                        <FormItem label="Brief study title">
                                            <Field component={Input} name="briefName" serverError={validation} />
                                        </FormItem>
                                        <FormItem label="Official (scientific) study title">
                                            <Field component={Input} name="scientificName" serverError={validation} />
                                        </FormItem>

                                        <FormItem label="Study type">
                                            <Field component={Choice} options={studyTypes} name="studyType" collapse serverError={validation} />
                                        </FormItem>

                                        <FormItem label="Method">
                                            <Field component={Choice} options={methodTypes} name="methodType" collapse serverError={validation} />
                                        </FormItem>
                                    </FormGroup>

                                    <FormGroup label="Study Status">
                                        <FormItem label="Status">
                                            <Field component={Select} options={recruitmentStatus} name="recruitmentStatus" serverError={validation} />
                                        </FormItem>

                                        <Stack>
                                            <FormItem label="Estimated Start Date">
                                                <Field
                                                    component={DatePicker}
                                                    name="estimatedStudyStartDate"
                                                    serverError={validation}
                                                    showMonthDropdown={true}
                                                    showYearDropdown={true}
                                                />
                                            </FormItem>

                                            <FormItem label="Estimated Completion Date">
                                                <Field
                                                    component={DatePicker}
                                                    name="estimatedStudyCompletionDate"
                                                    serverError={validation}
                                                    showMonthDropdown={true}
                                                    showYearDropdown={true}
                                                />
                                            </FormItem>
                                        </Stack>
                                    </FormGroup>

                                    <FormGroup label="Study Description">
                                        <FormItem label="Brief Summary">
                                            <p>Please include the objective, design, research question and the population of your study.</p>
                                            <Field component={Input} multiline={true} name="briefSummary" serverError={validation} />
                                        </FormItem>
                                    </FormGroup>

                                    <FormGroup label="Conditions and Keywords">
                                        <Field
                                            component={OntologyConceptFormBlock}
                                            label="Primary Disease or Condition Being Studied in the Trial, or the Focus of the Study"
                                            name="conditions"
                                            serverError={validation}
                                        />
                                        <FormItem label="Keywords">
                                            <Field component={LocalizedTextInput} name="keywords" languages={languages} serverError={validation} />
                                        </FormItem>
                                        <FormItem label="Intervention">
                                            <Field component={Input} name="intervention" serverError={validation} />
                                        </FormItem>
                                    </FormGroup>

                                    <FormGroup label="Enrollment">
                                        <FormItem label="Estimated total number of participants">
                                            <Field component={Input} name="estimatedEnrollment" inputMode="numeric" serverError={validation} />
                                        </FormItem>
                                    </FormGroup>
                                </div>

                                <div className="FormButtons">
                                    <Stack distribution="trailing">
                                        <Button type="submit" disabled={isSubmitting}>
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

const studyTypes = [
    { value: 'interventional', labelText: 'Interventional' },
    { value: 'observational', labelText: 'Observational' },
];

const methodTypes = [
    { value: 'survey', labelText: 'Survey' },
    { value: 'registry', labelText: 'Registry' },
    { value: 'rct', labelText: 'RCT' },
    { value: 'other', labelText: 'Other' },
];

export const recruitmentStatus = [
    { value: 'not_yet_recruiting', label: 'Not yet recruiting' },
    { value: 'recruiting', label: 'Recruiting' },
    { value: 'enrolling_by_invitation', label: 'Enrolling by invitation' },
    { value: 'active_not_recruiting', label: 'Active, not recruiting' },
    { value: 'suspended', label: 'Suspended' },
    { value: 'terminated', label: 'Terminated' },
    { value: 'completed', label: 'Completed' },
    { value: 'withdrawn', label: 'Withdrawn' },
];

const defaultData = {
    id: null,
    briefName: '',
    scientificName: '',
    briefSummary: '',
    studyType: '',
    condition: '',
    intervention: '',
    estimatedEnrollment: '',
    estimatedStudyStartDate: '',
    estimatedStudyCompletionDate: '',
    summary: '',
    recruitmentStatus: null,
    methodType: '',
    conditions: [],
    keyword: [
        {
            text: '',
            language: null,
        },
    ],
};

const StudyDetailsSchema = Yup.object().shape({
    briefName: Yup.string().required('Please enter a brief study title'),
    scientificName: Yup.string().nullable(),
    studyType: Yup.string().required('Please select the study type'),
    methodType: Yup.string().required('Please select the method'),
    status: Yup.string().nullable(),
    estimatedStudyStartDate: Yup.string().nullable(),
    estimatedStudyCompletionDate: Yup.string().nullable(),
    briefSummary: Yup.string().required('Please enter a brief summary'),
    conditions: Yup.array()
        .of(
            Yup.object().shape({
                code: Yup.string().required('This concept cannot be added'),
                url: Yup.string().required('This concept cannot be added'),
                displayName: Yup.string().required('This concept cannot be added'),
                ontology: Yup.string().required('Please select an ontology'),
            })
        )
        .nullable(),
    keywords: Yup.array()
        .of(
            Yup.object().shape({
                language: Yup.string().required('Please select a language'),
                text: Yup.string().required('Please enter a keyword'),
            })
        )
        .nullable(),
    intervention: Yup.string().nullable(),
    estimatedEnrollment: Yup.number().typeError('Please enter a valid number').nullable(),
});

import React, {Component} from 'react';
import {Button, LoadingOverlay, Stack} from "@castoredc/matter";
import {ValidatorForm} from 'react-form-validator-core';

import './Form.scss'
import moment from "moment";
import {toast} from "react-toastify";
import ToastContent from "../ToastContent";
import axios from "axios";
import FormItem from "./FormItem";
import Input from "../Input";
import Dropdown from "../Input/Dropdown";
import RadioGroup from "components/Input/RadioGroup";
import FormGroup from "components/Form/FormGroup";

export default class StudyDetailsForm extends Component {
    constructor(props) {
        super(props);

        this.state = {
            data: {
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
            },
            validation: {},
            isSaved: false,
            submitDisabled: false,
            isLoading: false,
        };
    }

    getMetadata = () => {
        const {data} = this.state;

        this.setState({
            isLoading: true,
        });

        axios.get('/api/study/' + this.props.studyId)
            .then((response) => {
                let metadata = response.data.metadata;

                if (metadata != null) {
                    if (metadata.estimatedStudyStartDate !== '') {
                        let estimatedStudyStartDate = moment(metadata.estimatedStudyStartDate, 'YYYY-MM-DD');
                        metadata.estimatedStudyStartDate = estimatedStudyStartDate.format("DD-MM-YYYY");
                    }

                    if (metadata.estimatedStudyCompletionDate !== '') {
                        let estimatedStudyCompletionDate = moment(metadata.estimatedStudyCompletionDate, 'YYYY-MM-DD');
                        metadata.estimatedStudyCompletionDate = estimatedStudyCompletionDate.format("DD-MM-YYYY");
                    }
                }

                this.setState({
                    data: {...data, ...metadata},
                    isLoading: false,
                });
            })
            .catch((error) => {
                this.setState({
                    isLoading: false,
                });

                if (error.response && typeof error.response.data.error !== "undefined") {
                    toast.error(<ToastContent type="error" message={error.response.data.error}/>);
                } else {
                    toast.error(<ToastContent type="error" message="An error occurred"/>);
                }
            });
    };

    componentDidMount() {
        ValidatorForm.addValidationRule('isDate', (value) => {
            var regex = /^([0-2][0-9]|(3)[0-1])(-)(((0)[0-9])|((1)[0-2]))(-)\d{4}$/i;
            return regex.test(value);
        });

        this.getMetadata();
    }

    handleChange = (event, callback = (() => {
    })) => {
        const {data, changedFieldsSinceFormSubmission} = this.state;
        this.setState({
            data: {
                ...data,
                [event.target.name]: event.target.value,
            },
            changedFieldsSinceFormSubmission: {
                ...changedFieldsSinceFormSubmission,
                [event.target.name]: true,
            },
            validation: {
                [event.target.name]: false,
            },
        }, callback);
    };

    handleSelectChange = (name, event) => {
        this.handleChange({
            target: {
                name: name,
                value: event.value,
            },
        });
    };

    handleSubmit = (event) => {
        const {studyId, admin = false, onSave} = this.props;
        const {data} = this.state;

        event.preventDefault();

        if (this.form.isFormValid()) {
            this.setState({
                submitDisabled: true,
                isLoading: true,
            });

            let estimatedStudyStartDate = moment(data.estimatedStudyStartDate, 'DD-MM-YYYY');
            let estimatedStudyCompletionDate = moment(data.estimatedStudyCompletionDate, 'DD-MM-YYYY');

            axios.post('/api/study/' + studyId + '/metadata' + (data.id ? '/' + data.id : ''), {
                briefName: data.briefName,
                scientificName: data.scientificName,
                briefSummary: data.briefSummary,
                type: data.studyType,
                condition: data.condition,
                intervention: data.intervention,
                estimatedEnrollment: data.estimatedEnrollment,
                estimatedStudyStartDate: estimatedStudyStartDate.format('YYYY-MM-DD'),
                estimatedStudyCompletionDate: estimatedStudyCompletionDate.format('YYYY-MM-DD'),
                summary: data.summary,
                recruitmentStatus: data.recruitmentStatus,
                methodType: data.methodType,
            })
                .then((response) => {
                    this.setState({
                        isSaved: true,
                        isLoading: false,
                        submitDisabled: false,
                    });

                    if (admin) {
                        toast.success(<ToastContent type="success"
                                                    message="The study details are saved successfully"/>, {
                            position: "top-right",
                        });

                        onSave();
                        window.scrollTo(0, 0);
                    }
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
                    this.setState({
                        submitDisabled: false,
                        isLoading: false,
                    });
                });
        }

        return false;
    };

    render() {
        const {catalog, studyId, admin = false} = this.props;
        const {isSaved, isLoading, submitDisabled} = this.state;
        const required = "This field is required";
        const invalid = "This value is invalid";

        if (isLoading && !submitDisabled) {
            return <LoadingOverlay accessibleLabel="Loading study information" />;
        }

        return (
            <ValidatorForm
                className="FullHeightForm"
                ref={node => (this.form = node)}
                onSubmit={this.handleSubmit}
                method="post"
            >
                <div className="FormContent">
                    <FormGroup label="Study Identification">

                        <FormItem label="Brief study title">
                            <Input
                                validators={['required']}
                                errorMessages={[required]}
                                name="briefName"
                                onChange={this.handleChange}
                                value={this.state.data.briefName}
                                serverError={this.state.validation.briefName}
                            />
                        </FormItem>
                        <FormItem label="Official (scientific) study title">
                            <Input
                                validators={['required']}
                                errorMessages={[required]}
                                name="scientificName"
                                onChange={this.handleChange}
                                value={this.state.data.scientificName}
                                serverError={this.state.validation.scientificName}
                            />
                        </FormItem>

                        <FormItem label="Study type">
                            <RadioGroup
                                variant="horizontal"
                                validators={['required']}
                                errorMessages={[required]}
                                options={studyTypes}
                                name="studyType"
                                onChange={this.handleChange}
                                value={this.state.data.studyType}
                                serverError={this.state.validation.studyType}
                            />
                        </FormItem>

                        <FormItem label="Method">
                            <RadioGroup
                                variant="horizontal"
                                validators={['required']}
                                errorMessages={[required]}
                                options={methodTypes}
                                name="methodType"
                                onChange={this.handleChange}
                                value={this.state.data.methodType}
                                serverError={this.state.validation.methodType}
                            />
                        </FormItem>
                    </FormGroup>

                    <FormGroup label="Study Status">

                        <FormItem label="Status">
                            <Dropdown
                                options={recruitmentStatus}
                                name="recruitmentStatus"
                                onChange={(e) => {
                                    this.handleSelectChange('recruitmentStatus', e)
                                }}
                                value={recruitmentStatus.filter(({value}) => value === this.state.data.recruitmentStatus)}
                                serverError={this.state.validation.recruitmentStatus}
                            />
                        </FormItem>

                        <Stack>
                            <FormItem label="Estimated Start Date">
                                <Input
                                    placeholder="DD-MM-YYYY"
                                    validators={['required', 'isDate']}
                                    errorMessages={[required, invalid]}
                                    name="estimatedStudyStartDate"
                                    onChange={this.handleChange}
                                    value={this.state.data.estimatedStudyStartDate}
                                    mask={[/\d/, /\d/, '-', /\d/, /\d/, '-', /\d/, /\d/, /\d/, /\d/]}
                                    serverError={this.state.validation.estimatedStudyStartDate}
                                />
                            </FormItem>

                            <FormItem label="Estimated Completion Date">
                                <Input
                                    placeholder="DD-MM-YYYY"
                                    validators={['required', 'isDate']}
                                    errorMessages={[required, invalid]}
                                    name="estimatedStudyCompletionDate"
                                    onChange={this.handleChange}
                                    value={this.state.data.estimatedStudyCompletionDate}
                                    mask={[/\d/, /\d/, '-', /\d/, /\d/, '-', /\d/, /\d/, /\d/, /\d/]}
                                    serverError={this.state.validation.estimatedStudyCompletionDate}
                                />
                            </FormItem>
                        </Stack>
                    </FormGroup>

                    <FormGroup label="Study Description">

                        <FormItem label="Brief Summary">
                            <p>
                                Please include the objective, design, research question and the population of your
                                study.
                            </p>
                            <Input
                                validators={['required']}
                                errorMessages={[required]}
                                name="briefSummary"
                                onChange={this.handleChange}
                                value={this.state.data.briefSummary}
                                serverError={this.state.validation.briefSummary}
                                as="textarea" rows="5"
                            />
                        </FormItem>
                    </FormGroup>

                    <FormGroup label="Conditions and Keywords">

                        <FormItem
                            label="Primary Disease or Condition Being Studied in the Trial, or the Focus of the Study">
                            <Input
                                name="condition"
                                onChange={this.handleChange}
                                value={this.state.data.condition}
                                serverError={this.state.validation.condition}
                            />
                        </FormItem>
                        {/*Keywords*/}
                        <FormItem label="Intervention">
                            <Input
                                name="intervention"
                                onChange={this.handleChange}
                                value={this.state.data.intervention}
                                serverError={this.state.validation.intervention}
                            />
                        </FormItem>
                    </FormGroup>

                    <FormGroup label="Enrollment">
                        <FormItem label="Estimated total number of participants">
                            <Input
                                validators={['required', 'isNumber']}
                                errorMessages={[required, invalid]}
                                name="estimatedEnrollment"
                                onChange={this.handleChange}
                                value={this.state.data.estimatedEnrollment}
                                serverError={this.state.validation.estimatedEnrollment}
                            />
                        </FormItem>
                    </FormGroup>
                </div>

                <div className="FormButtons">
                    <Stack distribution="trailing">
                        <Button type="submit" disabled={this.state.submitDisabled}>
                            Save
                        </Button>
                    </Stack>
                </div>

            </ValidatorForm>
        );
    }
}

const studyTypes = [
    {value: 'interventional', label: 'Interventional'},
    {value: 'observational', label: 'Observational'},
];

const methodTypes = [
    {value: 'survey', label: 'Survey'},
    {value: 'registry', label: 'Registry'},
    {value: 'rct', label: 'RCT'},
    {value: 'other', label: 'Other'},
];

export const recruitmentStatus = [
    {value: 'not_yet_recruiting', label: 'Not yet recruiting'},
    {value: 'recruiting', label: 'Recruiting'},
    {value: 'enrolling_by_invitation', label: 'Enrolling by invitation'},
    {value: 'active_not_recruiting', label: 'Active, not recruiting'},
    {value: 'suspended', label: 'Suspended'},
    {value: 'terminated', label: 'Terminated'},
    {value: 'completed', label: 'Completed'},
    {value: 'withdrawn', label: 'Withdrawn'},
];
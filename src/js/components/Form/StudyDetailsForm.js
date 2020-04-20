import React, {Component} from 'react';
import Button from 'react-bootstrap/Button'
import Row from 'react-bootstrap/Row'
import Col from 'react-bootstrap/Col'
import {ValidatorForm} from 'react-form-validator-core';

import './Form.scss'
import {Redirect} from "react-router-dom";
import {LinkContainer} from "react-router-bootstrap";
import moment from "moment";
import {toast} from "react-toastify";
import ToastContent from "../ToastContent";
import axios from "axios";
import FormItem from "./FormItem";
import Input from "../Input";
import Dropdown from "../Input/Dropdown";
import FormHeading from "./FormHeading";
import LoadingScreen from "../LoadingScreen";

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
                recruitmentStatus: '',
                methodType: ''
            },
            metadataSource: null,
            visitedFields: {},
            validation: {},
            isSaved: false,
            submitDisabled: false,
            isLoading: false
        };
    }

    getMetadata = () => {
        this.setState({
            isLoading: true,
        });

        axios.get('/api/study/' + this.props.studyId + '/metadata')
            .then((response) => {
                let metadata = response.data.metadata;

                if(metadata.estimatedStudyStartDate !== '')
                {
                    let estimatedStudyStartDate = moment(metadata.estimatedStudyStartDate, 'YYYY-MM-DD');
                    metadata.estimatedStudyStartDate = estimatedStudyStartDate.format("DD-MM-YYYY");
                }

                if(metadata.estimatedStudyCompletionDate !== '')
                {
                    let estimatedStudyCompletionDate = moment(metadata.estimatedStudyCompletionDate, 'YYYY-MM-DD');
                    metadata.estimatedStudyCompletionDate = estimatedStudyCompletionDate.format("DD-MM-YYYY");
                }

                this.setState({
                    data: metadata,
                    metadataSource: response.data.source,
                    isLoading: false
                });
            })
            .catch((error) => {
                this.setState({
                    isLoading: false,
                });

                if(error.response && typeof error.response.data.error !== "undefined")
                {
                    toast.error(<ToastContent type="error" message={error.response.data.error} />);
                } else {
                    toast.error(<ToastContent type="error" message="An error occurred" />);
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

    handleChange = (event, callback = (() => {})) => {
        const { data, changedFieldsSinceFormSubmission } = this.state;
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
            }
        }, callback);
    };

    handleSelectChange = (name, event) => {
        this.handleChange({
            target: {
                name: name,
                value: event.value
            }
        });
    };

    handleFieldVisit = (event) => {
        const { visitedFields } = this.state;
        this.setState({
            visitedFields: {
                ...visitedFields,
                [event.target.name]: true,
            },
        });
    };

    handleSubmit = (event) => {
        event.preventDefault();

        this.setState({
            submitDisabled: true,
            isLoading: true
        });

        if(this.form.isFormValid()) {
            let estimatedStudyStartDate = moment(this.state.data.estimatedStudyStartDate, 'DD-MM-YYYY');
            let estimatedStudyCompletionDate = moment(this.state.data.estimatedStudyCompletionDate, 'DD-MM-YYYY');

            let url = '/api/study/' + this.props.studyId + '/metadata/' + (this.state.metadataSource === 'database' ? this.state.data.id + '/update' : 'add');

            axios.post(url, {
                briefName:                    this.state.data.briefName,
                scientificName:               this.state.data.scientificName,
                briefSummary:                 this.state.data.briefSummary,
                type:                         this.state.data.studyType,
                condition:                    this.state.data.condition,
                intervention:                 this.state.data.intervention,
                estimatedEnrollment:          this.state.data.estimatedEnrollment,
                estimatedStudyStartDate:      estimatedStudyStartDate.format('YYYY-MM-DD'),
                estimatedStudyCompletionDate: estimatedStudyCompletionDate.format('YYYY-MM-DD'),
                summary:                      this.state.data.summary,
                recruitmentStatus:            this.state.data.recruitmentStatus,
                methodType:                   this.state.data.methodType,
            })
                .then((response) => {
                    this.setState({
                        isSaved: true,
                    });
                })
                .catch((error) => {
                    if (error.response && error.response.status === 400) {
                        this.setState({
                            validation: error.response.data.fields
                        });
                    } else {
                        toast.error(<ToastContent type="error" message="An error occurred"/>, {
                            position: "top-center"
                        });
                    }
                    this.setState({
                        submitDisabled: false,
                        isLoading: false
                    });
                });
        }

        return false;
    };

    render() {
        const { catalog, studyId, action, admin } = this.props;

        const backUrl = admin ? '/admin/' + catalog : '/my-studies/' + catalog + '/study/add';
        const nextUrl = admin ? '/admin/' + catalog + '/study/' + studyId + '/metadata/' + action + '/centers' : '/my-studies/' + catalog + '/study/' + studyId + '/metadata/centers';

        const required = "This field is required";
        const invalid = "This value is invalid";

        if(this.state.isSaved)
        {
            return <Redirect push to={nextUrl} />;
        }

        return (
            <ValidatorForm
                ref={node => (this.form = node)}
                onSubmit={this.handleSubmit}
                method="post"
                // className="row"
            >
                {this.state.isLoading && <LoadingScreen showLoading={true}/>}
                <Row>
                    <Col md={6}>
                        <FormHeading label="Overview" />
                        <FormItem label="Brief Name">
                            <Input
                                validators={['required']}
                                errorMessages={[required]}
                                name="briefName"
                                onChange={this.handleChange}
                                onBlur={this.handleFieldVisit}
                                value={this.state.data.briefName}
                                serverError={this.state.validation.briefName}
                            />
                        </FormItem>
                        <FormItem label="Scientific Name">
                            <Input
                                validators={['required']}
                                errorMessages={[required]}
                                name="scientificName"
                                onChange={this.handleChange}
                                onBlur={this.handleFieldVisit}
                                value={this.state.data.scientificName}
                                serverError={this.state.validation.scientificName}
                            />
                        </FormItem>
                        <FormItem label="Brief Summary">
                            <p>
                                Please include the objective, design, research question and the population of your study.
                            </p>
                            <Input
                                validators={['required']}
                                errorMessages={[required]}
                                name="briefSummary"
                                onChange={this.handleChange}
                                onBlur={this.handleFieldVisit}
                                value={this.state.data.briefSummary}
                                serverError={this.state.validation.briefSummary}
                                as="textarea" rows="5"
                            />
                        </FormItem>
                        <FormItem label="Type">
                            <Dropdown
                                validators={['required']}
                                errorMessages={[required]}
                                options={studyTypes}
                                name="studyType"
                                onChange={(e) => {this.handleSelectChange('studyType', e)}}
                                onBlur={this.handleFieldVisit}
                                value={studyTypes.filter(({value}) => value === this.state.data.studyType)}
                                serverError={this.state.validation.studyType}
                            />
                        </FormItem>
                        <FormItem label="Method">
                            <Dropdown
                                validators={['required']}
                                errorMessages={[required]}
                                options={methodTypes}
                                name="methodType"
                                onChange={(e) => {this.handleSelectChange('methodType', e)}}
                                onBlur={this.handleFieldVisit}
                                value={methodTypes.filter(({value}) => value === this.state.data.methodType)}
                                serverError={this.state.validation.methodType}
                            />
                        </FormItem>
                        <FormItem label="Condition">
                            <Input
                                name="condition"
                                onChange={this.handleChange}
                                onBlur={this.handleFieldVisit}
                                value={this.state.data.condition}
                                serverError={this.state.validation.condition}
                            />
                        </FormItem>
                        <FormItem label="Intervention">
                            <Input
                                name="intervention"
                                onChange={this.handleChange}
                                onBlur={this.handleFieldVisit}
                                value={this.state.data.intervention}
                                serverError={this.state.validation.intervention}
                            />
                        </FormItem>
                        <FormHeading label="Enrollment" />
                        <Row>
                            <Col md={6}>
                                <FormItem label="Estimated Number of Patients">
                                    <Input
                                        validators={['required', 'isNumber']}
                                        errorMessages={[required, invalid]}
                                        name="estimatedEnrollment"
                                        onChange={this.handleChange}
                                        onBlur={this.handleFieldVisit}
                                        value={this.state.data.estimatedEnrollment}
                                        serverError={this.state.validation.estimatedEnrollment}
                                    />
                                </FormItem>
                            </Col>
                        </Row>
                        <FormHeading label="Duration" />
                        <Row>
                            <Col md={6}>
                                <FormItem label="Estimated Start Date">
                                    <Input
                                        placeholder="DD-MM-YYYY"
                                        validators={['required', 'isDate']}
                                        errorMessages={[required, invalid]}
                                        name="estimatedStudyStartDate"
                                        onChange={this.handleChange}
                                        onBlur={this.handleFieldVisit}
                                        value={this.state.data.estimatedStudyStartDate}
                                        mask={[/\d/, /\d/, '-', /\d/, /\d/, '-', /\d/, /\d/, /\d/, /\d/]}
                                        serverError={this.state.validation.estimatedStudyStartDate}
                                    />
                                </FormItem>
                            </Col>
                            <Col md={6}>
                                <FormItem label="Estimated Completion Date">
                                    <Input
                                        placeholder="DD-MM-YYYY"
                                        validators={['required', 'isDate']}
                                        errorMessages={[required, invalid]}
                                        name="estimatedStudyCompletionDate"
                                        onChange={this.handleChange}
                                        onBlur={this.handleFieldVisit}
                                        value={this.state.data.estimatedStudyCompletionDate}
                                        mask={[/\d/, /\d/, '-', /\d/, /\d/, '-', /\d/, /\d/, /\d/, /\d/]}
                                        serverError={this.state.validation.estimatedStudyCompletionDate}
                                    />
                                </FormItem>
                            </Col>
                        </Row>
                    </Col>
                    <Col md={6}>
                        {admin && <div>
                            <FormHeading label="Admin" />
                            <FormItem label="Status">
                                <Dropdown
                                    options={recruitmentStatus}
                                    name="recruitmentStatus"
                                    onChange={(e) => {this.handleSelectChange('recruitmentStatus', e)}}
                                    onBlur={this.handleFieldVisit}
                                    value={recruitmentStatus.filter(({value}) => value === this.state.data.recruitmentStatus)}
                                    serverError={this.state.validation.recruitmentStatus}
                                />
                            </FormItem>
                            <FormItem label="Long Summary">
                                <Input
                                    name="summary"
                                    onChange={this.handleChange}
                                    onBlur={this.handleFieldVisit}
                                    value={this.state.data.summary}
                                    serverError={this.state.validation.summary}
                                    as="textarea" rows="15"
                                />
                            </FormItem>
                        </div>}
                    </Col>
                </Row>


                <Row className="FullScreenSteppedFormButtons">
                    <Col>
                        <LinkContainer to={backUrl}>
                            <Button variant="secondary">Back</Button>
                        </LinkContainer>
                    </Col>
                    <Col>
                        <Button variant="primary" type="submit" disabled={this.state.submitDisabled}>Next</Button>
                    </Col>
                </Row>

            </ValidatorForm>
        );
    }
}

const studyTypes = [
    { value: 'interventional', label: 'Interventional' },
    { value: 'observational', label: 'Observational' },
];

const methodTypes = [
    { value: 'survey', label: 'Survey' },
    { value: 'registry', label: 'Registry' },
    { value: 'rct', label: 'RCT' },
    { value: 'other', label: 'Other' },
];

export const recruitmentStatus = [
    { value: 'not_yet_recruiting', label: 'Not yet recruiting' },
    { value: 'recruiting', label: 'Recruiting' },
    { value: 'enrolling_by_invitation', label: 'Enrolling by invitation' },
    { value: 'active_not_recruiting', label: 'Active, not recruiting' },
    { value: 'suspended', label: 'Suspended' },
    { value: 'terminated', label: 'Terminated' },
    { value: 'completed', label: 'Completed' },
    { value: 'withdrawn', label: 'Withdrawn' }
];
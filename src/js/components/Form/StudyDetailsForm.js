import React, { Component } from 'react';
import Button from 'react-bootstrap/Button'
import Row from 'react-bootstrap/Row'
import Col from 'react-bootstrap/Col'
import { ValidatorForm } from 'react-form-validator-core';

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
                estimatedStudyCompletionDate: ''
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

    handleStudyTypeChange = (event) => {
        this.handleChange({
            target: {
                name: 'studyType',
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
                estimatedStudyCompletionDate: estimatedStudyCompletionDate.format('YYYY-MM-DD')
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
        const { catalog, studyId } = this.props;

        const required = "This field is required";
        const invalid = "This value is invalid";

        if(this.state.isSaved)
        {
            return <Redirect push to={'/my-studies/' + catalog + '/study/' + studyId + '/metadata/centers'} />;
        }

        return (
            <ValidatorForm
                ref={node => (this.form = node)}
                onSubmit={this.handleSubmit}
                method="post"
            >
                {this.state.isLoading && <LoadingScreen showLoading={true}/>}

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
                            onChange={this.handleStudyTypeChange}
                            onBlur={this.handleFieldVisit}
                            value={studyTypes.filter(({value}) => value === this.state.data.studyType)}
                            serverError={this.state.validation.studyType}
                        />
                    </FormItem>
                    <FormItem label="Condition">
                        <Input
                            validators={['required']}
                            errorMessages={[required]}
                            name="condition"
                            onChange={this.handleChange}
                            onBlur={this.handleFieldVisit}
                            value={this.state.data.condition}
                            serverError={this.state.validation.condition}
                        />
                    </FormItem>
                    <FormItem label="Intervention">
                        <Input
                            validators={['required']}
                            errorMessages={[required]}
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
                </Col>


                <Row className="FullScreenSteppedFormButtons">
                    <Col>
                        <LinkContainer to={'/my-studies/' + catalog + '/study/add'}>
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
    { value: 'registry', label: 'Registry' }
];
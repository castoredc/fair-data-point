import React, { Component } from 'react';
import Button from 'react-bootstrap/Button'
import Row from 'react-bootstrap/Row'
import Col from 'react-bootstrap/Col'
import { ValidatorForm } from 'react-form-validator-core';

import './Form.scss'
import {generatePath, Redirect} from "react-router-dom";
import {LinkContainer} from "react-router-bootstrap";
import moment from "moment";
import {toast} from "react-toastify";
import ToastContent from "../ToastContent";
import LoadingSpinner from "../LoadingScreen/LoadingSpinner";
import axios from "axios";
import FormItem from "./FormItem";
import Input from "../Input";
import Dropdown from "../Input/Dropdown";
import FormHeading from "./FormHeading";
import FullScreenSteppedForm from "./FullScreenSteppedForm";


export default class StudyDetailsForm extends Component {
    constructor(props) {
        super(props);

        this.state = {
            data: {
                briefName: '',
                scientificName: '',
                briefSummary: '',
                summary: '',
                studyType: '',
                condition: '',
                intervention: '',
                estimatedEnrollment: '',
                estimatedStartDate: '',
                estimatedCompletionDate: ''
            },
            forceValidation: false,
            visitedFields: {},
            changedFieldsSinceFormSubmission: {},
            validation: {},
            study: null,
            studies: {},
            isSaved: false,
            submitDisabled: false
        };
    }

    componentDidMount() {
        ValidatorForm.addValidationRule('isDate', (value) => {
            var regex = /^([0-2][0-9]|(3)[0-1])(-)(((0)[0-9])|((1)[0-2]))(-)\d{4}$/i;
            return regex.test(value);
        });
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
                value: event
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

        window.onbeforeunload = null;

        this.setState({
            submitDisabled: true
        });

        if(this.form.isFormValid()) {
            let estimatedStartDate = moment(this.state.data.estimatedStartDate, 'DD-MM-YYYY');
            let estimatedCompletionDate = moment(this.state.data.estimatedCompletionDate, 'DD-MM-YYYY');

            console.log(this.state.data.studyType);

            axios.post('/api/studies/metadata/add', {
                studyId: this.props.studyId,
                briefName: this.state.data.briefName,
                scientificName: this.state.data.scientificName,
                briefSummary: this.state.data.briefSummary,
                summary: this.state.data.summary,
                type: this.state.data.studyType.value,
                condition: this.state.data.condition,
                intervention: this.state.data.intervention,
                estimatedEnrollment: this.state.data.estimatedEnrollment,
                estimatedStartDate: estimatedStartDate.format('YYYY-MM-DD'),
                estimatedCompletionDate: estimatedCompletionDate.format('YYYY-MM-DD')
            })
                .then((response) => {
                    this.setState({
                        isSaved: true
                    });
                })
                .catch((error) => {
                    if (error.response && error.response.status === 400)
                    {
                        this.setState({
                            validation: error.response.data.fields
                        });
                    }
                    else
                    {
                        toast.error(<ToastContent type="error" message="An error occurred" />, {
                            position: "top-center"
                        });
                    }
                    this.setState({
                        submitDisabled: false
                    });
                });
        }

        return false;
    };

    render() {
        const { studyId } = this.props;

        const required = "This field is required";
        const invalid = "This value is invalid";

        const studyTypes = [
            { value: 'interventional', label: 'Interventional' },
            { value: 'observational', label: 'Observational' },
            { value: 'registry', label: 'Registry' }
        ];

        if(this.state.isSaved)
        {
            return <Redirect to={'/my-studies/study/' + studyId + '/metadata/organizations'} />;
        }

        return (
            <ValidatorForm
                ref={node => (this.form = node)}
                onSubmit={this.handleSubmit}
                method="post"
            >
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
                    <FormItem label="Summary">
                        <Input
                            name="Summary"
                            onChange={this.handleChange}
                            onBlur={this.handleFieldVisit}
                            value={this.state.data.Summary}
                            serverError={this.state.validation.Summary}
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
                            value={this.state.data.studyType}
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
                                    name="estimatedStartDate"
                                    onChange={this.handleChange}
                                    onBlur={this.handleFieldVisit}
                                    value={this.state.data.estimatedStartDate}
                                    mask={[/\d/, /\d/, '-', /\d/, /\d/, '-', /\d/, /\d/, /\d/, /\d/]}
                                    serverError={this.state.validation.estimatedEnrollment}
                                />
                            </FormItem>
                        </Col>
                        <Col md={6}>
                            <FormItem label="Estimated Completion Date">
                                <Input
                                    placeholder="DD-MM-YYYY"
                                    validators={['required', 'isDate']}
                                    errorMessages={[required, invalid]}
                                    name="estimatedCompletionDate"
                                    onChange={this.handleChange}
                                    onBlur={this.handleFieldVisit}
                                    value={this.state.data.estimatedCompletionDate}
                                    mask={[/\d/, /\d/, '-', /\d/, /\d/, '-', /\d/, /\d/, /\d/, /\d/]}
                                    serverError={this.state.validation.estimatedCompletionDate}
                                />
                            </FormItem>
                        </Col>
                    </Row>
                </Col>
                <Col md={6}>
                </Col>


                <div className="FullScreenSteppedFormButtons">
                    <Button variant="secondary">Back</Button>
                    <Button variant="primary" type="submit" disabled={this.state.submitDisabled}>Next</Button>
                </div>

            </ValidatorForm>
        );
    }
}

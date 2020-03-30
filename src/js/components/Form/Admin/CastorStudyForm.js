import React, {Component} from 'react';
import Button from 'react-bootstrap/Button'
import Row from 'react-bootstrap/Row'
import Col from 'react-bootstrap/Col'
import {ValidatorForm} from 'react-form-validator-core';

import './../Form.scss'
import {Redirect} from "react-router-dom";
import {LinkContainer} from "react-router-bootstrap";
import {toast} from "react-toastify";
import ToastContent from "../../ToastContent";
import axios from "axios";
import FormItem from "./../FormItem";
import FormHeading from "../FormHeading";
import Input from "../../Input";

export default class CastorStudyForm extends Component {
    constructor(props) {
        super(props);

        this.state = {
            data: {
                studyId: '',
                studyName: '',
                studySlug: ''
            },
            metadataSource: null,
            visitedFields: {},
            validation: {},
            isSaved: false,
            submitDisabled: false
        };
    }

    handleChange = (event, callback = (() => {})) => {
        const { data, changedFieldsSinceFormSubmission } = this.state;
        const newState = {
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
        };
        this.setState(newState, callback);
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
            axios.post('/api/study/add/manual', {
                studyId:                    this.state.data.studyId,
                studyName:               this.state.data.studyName,
                studySlug:                 this.state.data.studySlug
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
        const { catalog } = this.props;

        const required = "This field is required";

        if(this.state.isSaved)
        {
            return <Redirect push to={'/my-studies/' + catalog + '/study/' + this.state.data.studyId + '/metadata/details'} />;
        }

        return (
            <ValidatorForm
                ref={node => (this.form = node)}
                onSubmit={this.handleSubmit}
                method="post"
            >
                <Row>
                    <Col md={12}>
                        <FormHeading label="Study information" />
                    </Col>
                    <Col md={4}>
                        <FormItem label="Study ID">
                            <Input
                                validators={['required']}
                                errorMessages={[required]}
                                name="studyId"
                                onChange={this.handleChange}
                                onBlur={this.handleFieldVisit}
                                value={this.state.data.studyId}
                                serverError={this.state.validation.studyId}
                            />
                        </FormItem>

                        <FormItem label="Study Name">
                            <Input
                                validators={['required']}
                                errorMessages={[required]}
                                name="studyName"
                                onChange={this.handleChange}
                                onBlur={this.handleFieldVisit}
                                value={this.state.data.studyName}
                                serverError={this.state.validation.studyName}
                            />
                        </FormItem>

                        <FormItem label="Study Slug">
                            <Input
                                validators={['required']}
                                errorMessages={[required]}
                                name="studySlug"
                                onChange={this.handleChange}
                                onBlur={this.handleFieldVisit}
                                value={this.state.data.studySlug}
                                serverError={this.state.validation.studySlug}
                            />
                        </FormItem>
                    </Col>
                </Row>

                <Row className="FullScreenSteppedFormButtons">
                    <Col>
                        <LinkContainer to={'/admin/' + catalog} exact={true}>
                            <Button variant="secondary">Back</Button>
                        </LinkContainer>
                    </Col>
                    <Col>
                        <Button variant="primary" type="submit" disabled={this.state.submitDisabled}>Add study</Button>
                    </Col>
                </Row>

            </ValidatorForm>
        );
    }
}
import React, {Component} from 'react';
import Button from 'react-bootstrap/Button'
import Row from 'react-bootstrap/Row'
import Col from 'react-bootstrap/Col'
import {ValidatorForm} from 'react-form-validator-core';

import '../../Form.scss'
import {Redirect} from "react-router-dom";
import {LinkContainer} from "react-router-bootstrap";
import {toast} from "react-toastify";
import ToastContent from "../../../ToastContent";
import axios from "axios";
import FormItem from "./../../FormItem";
import FormHeading from "../../FormHeading";
import Input from "../../../Input";
import Dropdown from "../../../Input/Dropdown";
import {recruitmentStatus} from "../../StudyDetailsForm";
import {CheckboxGroup} from "../../../Input/Checkbox";
import RadioGroup from "../../../RadioGroup";
import {localizedText} from "../../../../util";
import Spinner from "react-bootstrap/Spinner";

export default class DistributionForm extends Component {
    constructor(props) {
        super(props);

        let data = {
            title: '',
            type: '',
            version: '',
            slug: '',
            description: '',
            language: null,
            license: null,
            accessRights: null,
            includeAllData: null
        };

        if (typeof props.distribution !== 'undefined') {
            data = {
                title: localizedText(props.distribution.title, 'en'),
                type: props.distribution.type,
                version: props.distribution.version,
                slug: props.distribution.slug,
                description: localizedText(props.distribution.description, 'en'),
                language: props.distribution.language.code,
                license: props.distribution.license.slug,
                accessRights: props.distribution.accessRights,
                update: true,
                includeAllData: props.distribution.includeAll
            }
        }

        this.state = {
            data: data,
            visitedFields: {},
            validation: {},
            isSaved: false,
            submitDisabled: false,
            languages: [],
            licenses: [],
            update: (typeof props.distribution !== 'undefined')
        };
    }

    componentDidMount() {
        this.getLanguages();
        this.getLicenses();
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

    getLanguages = () => {
        axios.get('/api/languages')
            .then((response) => {
                this.setState({
                    languages: response.data,
                });
            })
            .catch((error) => {
                toast.error(<ToastContent type="error" message="An error occurred" />);
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
                toast.error(<ToastContent type="error" message="An error occurred" />);
            });
    };

    handleSubmit = (event) => {
        const { catalog, dataset, distribution } = this.props;
        event.preventDefault();

        const { update } = this.state;

        if(this.form.isFormValid()) {
            this.setState({
                submitDisabled: true,
                isLoading: true
            });

            const url = '/api/catalog/' + catalog + '/dataset/' + dataset + '/distribution/' + (update ? distribution.slug + '/update' : 'add');

           axios.post(url, this.state.data)
                .then(() => {
                    this.setState({
                        isSaved: true,
                        isLoading: false,
                        submitDisabled: false
                    });

                    if (update) {
                        toast.success(<ToastContent type="success" message="The distribution details are saved successfully" />, {
                            position: "top-right"
                        });
                    }
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
        const { catalog, dataset } = this.props;
        const { languages, licenses, isSaved, update, isLoading } = this.state;

        const required = "This field is required";

        if(isSaved && !update)
        {
            return <Redirect push to={'/admin/catalog/' + catalog + '/dataset/' + dataset + '/distributions'} />;
        }

        return (
            <ValidatorForm
                ref={node => (this.form = node)}
                onSubmit={this.handleSubmit}
                method="post"
            >
                <Row>
                    <Col md={12}>
                        <FormHeading label="Distribution information" />
                    </Col>
                    <Col md={6}>
                        <FormItem label="Title">
                            <Input
                                validators={['required']}
                                errorMessages={[required]}
                                name="title"
                                onChange={this.handleChange}
                                onBlur={this.handleFieldVisit}
                                value={this.state.data.title}
                                serverError={this.state.validation.title}
                            />
                        </FormItem>

                        <FormItem label="Type">
                            <RadioGroup
                                options={distributionTypes}
                                onChange={this.handleChange}
                                onBlur={this.handleFieldVisit}
                                value={this.state.data.type}
                                variant="horizontal"
                                name="type"
                            />
                        </FormItem>

                        {this.state.data.type === 'csv' && <FormItem label="Available data">
                            <RadioGroup
                                options={availableData}
                                onChange={this.handleChange}
                                onBlur={this.handleFieldVisit}
                                value={this.state.data.includeAllData}
                                variant="horizontal"
                                name="includeAllData"
                            />
                        </FormItem>}

                        <FormItem label="Version">
                            <Input
                                validators={['required']}
                                errorMessages={[required]}
                                name="version"
                                onChange={this.handleChange}
                                onBlur={this.handleFieldVisit}
                                value={this.state.data.version}
                                serverError={this.state.validation.version}
                            />
                        </FormItem>

                        <FormItem label="Slug">
                            <Input
                                validators={['required']}
                                errorMessages={[required]}
                                name="slug"
                                onChange={this.handleChange}
                                onBlur={this.handleFieldVisit}
                                value={this.state.data.slug}
                                serverError={this.state.validation.slug}
                            />
                        </FormItem>
                    </Col>
                    <Col md={6}>
                        <FormItem label="Description">
                            <Input
                                name="description"
                                onChange={this.handleChange}
                                onBlur={this.handleFieldVisit}
                                value={this.state.data.description}
                                serverError={this.state.validation.description}
                                as="textarea" rows="8"
                            />
                        </FormItem>
                    </Col>

                    <Col md={6}>
                        <FormItem label="Language">
                            <Dropdown
                                validators={['required']}
                                errorMessages={[required]}
                                options={languages}
                                name="language"
                                onChange={(e) => {this.handleSelectChange('language', e)}}
                                onBlur={this.handleFieldVisit}
                                value={languages.filter(({value}) => value === this.state.data.language)}
                                serverError={this.state.validation.language}
                            />
                        </FormItem>

                        <FormItem label="License">
                            <Dropdown
                                validators={['required']}
                                errorMessages={[required]}
                                options={licenses}
                                name="license"
                                onChange={(e) => {this.handleSelectChange('license', e)}}
                                onBlur={this.handleFieldVisit}
                                value={licenses.filter(({value}) => value === this.state.data.license)}
                                serverError={this.state.validation.license}
                            />
                        </FormItem>

                        <FormItem label="Access type">
                            <Dropdown
                                validators={['required']}
                                errorMessages={[required]}
                                options={accessTypes}
                                name="accessRights"
                                onChange={(e) => {this.handleSelectChange('accessRights', e)}}
                                onBlur={this.handleFieldVisit}
                                value={accessTypes.filter(({value}) => value === this.state.data.accessRights)}
                                serverError={this.state.validation.accessRights}
                            />
                        </FormItem>
                    </Col>
                </Row>

                <Row className="FullScreenSteppedFormButtons">
                    <Col>
                    </Col>
                    <Col>
                        {update ? <Button variant="primary" type="submit" disabled={this.state.submitDisabled}>
                            {isLoading && <Spinner as="span" animation="border" size="sm" role="status" aria-hidden="true" />}
                            Update distribution
                        </Button> : <Button variant="primary" type="submit" disabled={this.state.submitDisabled}>
                            Add distribution
                        </Button>}
                    </Col>
                </Row>

            </ValidatorForm>
        );
    }
}

export const accessTypes = [
    { value: 1, label: 'Public' },
    { value: 2, label: 'Study Users' },
];

export const distributionTypes = [
    { value: 'csv', label: 'CSV Distribution' },
    { value: 'rdf', label: 'RDF Distribution' },
];

export const availableData = [
    { value: true, label: 'All data' },
    { value: false, label: 'Selection of data' },
];
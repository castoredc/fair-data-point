import React, {Component} from 'react';
import Row from 'react-bootstrap/Row'
import Col from 'react-bootstrap/Col'
import {ValidatorForm} from 'react-form-validator-core';

import '../Form.scss'
import {Redirect} from "react-router-dom";
import {toast} from "react-toastify";
import ToastContent from "../../ToastContent";
import axios from "axios";
import FormItem from "./../FormItem";
import Input from "../../Input";
import Dropdown from "../../Input/Dropdown";
import RadioGroup from "../../Input/RadioGroup";
import {mergeData} from "../../../util";
import {Button} from "@castoredc/matter";

export default class DistributionForm extends Component {
    constructor(props) {
        super(props);

        this.state = {
            data:           props.distribution ? mergeData(defaultData, props.distribution) : defaultData,
            visitedFields:  {},
            validation:     {},
            isSaved:        false,
            submitDisabled: false,
            languages:      [],
            licenses:       [],
            distribution:   props.distribution ? props.distribution : null,
            update:         !!props.distribution,
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

        const { data } = this.state;

        if(this.form.isFormValid()) {
            this.setState({
                submitDisabled: true,
                isLoading: true
            });

           const url = '/api/dataset/' + dataset + '/distribution' + (distribution ? '/' + distribution.slug : '');

           axios.post(url, data)
                .then((response) => {
                    this.setState({
                        isSaved: true,
                        isLoading: false,
                        submitDisabled: false,
                        distribution: response.data
                    });

                    if (distribution) {
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
        const { data, validation, licenses, isSaved, update, distribution, submitDisabled } = this.state;

        const required = "This field is required";

        if(isSaved && !update)
        {
            return <Redirect push to={'/admin/dataset/' + dataset + '/distribution/' + distribution.slug + '/metadata'} />;
        }

        return (
            <ValidatorForm
                ref={node => (this.form = node)}
                onSubmit={this.handleSubmit}
                method="post"
            >
                <FormItem label="Type">
                    <RadioGroup
                        options={distributionTypes}
                        onChange={this.handleChange}
                        value={data.type}
                        variant="horizontal"
                        name="type"
                    />
                </FormItem>

                {data.type === 'csv' && <FormItem label="Available data">
                    <RadioGroup
                        options={availableData}
                        onChange={this.handleChange}
                        value={data.includeAllData}
                        variant="horizontal"
                        name="includeAllData"
                    />
                </FormItem>}

                <FormItem label="Slug">
                    <Input
                        validators={['required']}
                        errorMessages={[required]}
                        name="slug"
                        onChange={this.handleChange}
                        value={data.slug}
                        serverError={validation.slug}
                    />
                </FormItem>

                <FormItem label="License">
                    <Dropdown
                        validators={['required']}
                        errorMessages={[required]}
                        options={licenses}
                        name="license"
                        onChange={(e) => {this.handleSelectChange('license', e)}}
                        value={licenses.filter(({value}) => value === data.license)}
                        serverError={validation.license}
                    />
                </FormItem>

                <FormItem label="Access type">
                    <Dropdown
                        validators={['required']}
                        errorMessages={[required]}
                        options={accessTypes}
                        name="accessRights"
                        onChange={(e) => {this.handleSelectChange('accessRights', e)}}
                        value={accessTypes.filter(({value}) => value === data.accessRights)}
                        serverError={validation.accessRights}
                    />
                </FormItem>

                <Row className="FullScreenSteppedFormButtons">
                    <Col>
                    </Col>
                    <Col>
                        {distribution ? <Button disabled={submitDisabled}>
                            Update distribution
                        </Button> : <Button disabled={submitDisabled}>
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

export const defaultData = {
    type: '',
    slug: '',
    accessRights: null,
    includeAllData: null,
    license: null
};

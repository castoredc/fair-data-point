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
import FormHeading from "../FormHeading";

export default class DistributionForm extends Component {
    constructor(props) {
        super(props);

        let data = props.distribution ? mergeData(defaultData, props.distribution) : defaultData;

        console.log(data);

        if(typeof data.dataModel === 'object' && data.dataModel !== '') {
            data = {
                ...data,
                dataModel: data.dataModel.dataModel
            };
        }

        this.state = {
            data:           data,
            visitedFields:  {},
            validation:     {},
            isSaved:        false,
            submitDisabled: false,
            languages:      [],
            licenses:       [],
            dataModels:     [],
            distribution:   props.distribution ? props.distribution : null,
            update:         !!props.distribution,
            showApiUser:    props.distribution ? ! props.distribution.hasApiUser : true
        };
    }

    componentDidMount() {
        this.getLanguages();
        this.getLicenses();
        this.getDataModels();
    }

    handleChange = (event) => {
        const { data } = this.state;
        const newState = {
            data: {
                ...data,
                [event.target.name]: event.target.value,
            },
            validation: {
                [event.target.name]: false,
            }
        };
        this.setState(newState);
    };

    handleTypeChange = (event) => {
        const { data } = this.state;

        this.setState({
            data: {
                ...data,
                includeAllData: '',
                dataModel: ''
            }
        }, () => {
            this.handleChange(event);
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

    getDataModels = () => {
        axios.get('/api/model')
            .then((response) => {
                this.setState({
                    dataModels: response.data.map((dataModel) => {
                        return { label: dataModel.title, value: dataModel.id }
                    }),
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

    showApiUser = () => {
        this.setState({
            showApiUser: true
        });
    };

    render() {
        const { dataset } = this.props;
        const { data, validation, licenses, dataModels, isSaved, update, distribution, submitDisabled, showApiUser } = this.state;

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
                        onChange={this.handleTypeChange}
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

                {data.type === 'rdf' && <FormItem label="Data model">
                    <Dropdown
                        validators={['required']}
                        errorMessages={[required]}
                        options={dataModels}
                        name="dataModel"
                        onChange={(e) => {this.handleChange({target: { name: 'dataModel', value: e.value }})}}
                        value={dataModels.filter(({value}) => value === data.dataModel)}
                        serverError={validation.dataModel}
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
                        onChange={(e) => {this.handleChange({target: { name: 'license', value: e.value }})}}
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
                        onChange={(e) => {this.handleChange({target: { name: 'accessRights', value: e.value }})}}
                        value={accessTypes.filter(({value}) => value === data.accessRights)}
                        serverError={validation.accessRights}
                    />
                </FormItem>

                {distribution && <FormItem label="Publish distribution">
                    <RadioGroup
                        validators={['required']}
                        errorMessages={[required]}
                        options={[
                            {
                                label: 'Yes',
                                value: true
                            },
                            {
                                label: 'No',
                                value: false
                            }
                        ]}
                        onChange={this.handleChange}
                        value={data.published}
                        variant="horizontal"
                        name="published"
                    />
                </FormItem>}

                <FormHeading label="Castor EDC API Credentials" />

                {showApiUser ? <div>
                    <FormItem label="Email address">
                        <Input
                            name="apiUser"
                            onChange={this.handleChange}
                            value={data.apiUser}
                            serverError={validation.apiUser}
                        />
                    </FormItem>

                    <FormItem label="Client ID">
                        <Input
                            name="clientId"
                            onChange={this.handleChange}
                            value={data.clientId}
                            serverError={validation.clientId}
                        />
                    </FormItem>

                    <FormItem label="Client Secret">
                        <Input
                            name="clientSecret"
                            onChange={this.handleChange}
                            value={data.clientSecret}
                            serverError={validation.clientSecret}
                        />
                    </FormItem>
                </div> : <div>
                    <p>The API Credentials for this distribution are encrypted.</p>

                    <Button buttonType="danger" onClick={this.showApiUser}>Change API Credentials</Button>
                </div>}

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
    dataModel: '',
    license: null,
    apiUser: '',
    clientId: '',
    clientSecret: '',
    published: false
};

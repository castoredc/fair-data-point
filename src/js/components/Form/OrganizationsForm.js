import React, {Component} from 'react';
import Button from 'react-bootstrap/Button'
import Row from 'react-bootstrap/Row'
import Col from 'react-bootstrap/Col'
import {ValidatorForm} from 'react-form-validator-core';

import './Form.scss'
import {Redirect} from "react-router-dom";
import {LinkContainer} from "react-router-bootstrap";
import {toast} from "react-toastify";
import ToastContent from "../ToastContent";
import axios from "axios";
import FormItem from "./FormItem";
import Input from "../Input";
import Dropdown from "../Input/Dropdown";
import FormHeading from "./FormHeading";
import LoadingScreen from "../LoadingScreen";
import Icon from "../Icon";

export default class OrganizationsForm extends Component {
    constructor(props) {
        super(props);

        this.state = {
            organizations: [
                {
                    name: '',
                    country: '',
                    city: '',
                    department: '',
                    additionalInformation: ''
                }
            ],
            countries: [],
            metadataSource: null,
            visitedFields: {},
            validation: [
                {
                    name: null,
                    country: null,
                    city: null,
                    department: null,
                    additionalInformation: null
                }
            ],
            isSaved: false,
            submitDisabled: false,
            isLoading: false,
        };
    }

    handleNewOrganization = () => {
        this.form.isFormValid(false).then(valid => {
            if (valid) {
                this.setState({
                    organizations: [
                        ...this.state.organizations,
                        {
                            name:                  '',
                            country:               '',
                            city:                  '',
                            department:            '',
                            additionalInformation: ''
                        }
                    ],
                    validation:    [
                        ...this.state.validation,
                        {
                            name:                  null,
                            country:               null,
                            city:                  null,
                            department:            null,
                            additionalInformation: null
                        }
                    ]
                });
            }
        });
    };

    removeOrganization = (index) => {

        const { organizations, validation } = this.state;

        organizations.splice(index, 1);
        validation.splice(index, 1);

        this.setState({
            organizations: organizations,
            validation: validation
        });
    };

    getCountries = () => {
        axios.get('/api/countries')
            .then((response) => {
                this.setState({
                    countries: response.data,
                });
            })
            .catch((error) => {
                if(error.response && typeof error.response.data.error !== "undefined")
                {
                    toast.error(<ToastContent type="error" message={error.response.data.error} />);
                } else {
                    toast.error(<ToastContent type="error" message="An error occurred" />);
                }
            });
    };

    getCenters = () => {
        this.setState({
            isLoading: true,
        });

        axios.get('/api/study/' + this.props.studyId + '/centers')
            .then((response) => {
                if(response.data.length !== 0)
                {
                    let validation = [];
                    for (let i = 0; i < response.data.length; i++) {
                        validation.push({
                            name: null,
                            country: null,
                            city: null,
                            department: null,
                            additionalInformation: null
                        });
                    }

                    this.setState({
                        organizations: response.data,
                        validation: validation
                    });
                }

                this.setState({
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
        this.getCountries();
        this.getCenters();
    }

    handleChange = (index, event, callback = (() => {})) => {
        const { organizations, validation } = this.state;

        organizations[index][event.target.name] = event.target.value;
        validation[index][event.target.name] = false;

        this.setState({
            organizations: organizations,
            validation: validation
        }, callback);
    };

    handleCountryChange = (index, event) => {
        this.handleChange(index, {
            target: {
                name: 'country',
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

        window.onbeforeunload = null;

        this.setState({
            submitDisabled: true,
            isLoading: true
        });

        if(this.form.isFormValid()) {
            axios.post('/api/study/' + this.props.studyId + '/centers/add', this.state.organizations)
                .then((response) => {
                    this.setState({
                        isSaved: true
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
            return <Redirect push to={'/my-studies/' + catalog + '/study/' + studyId + '/metadata/contacts'} />;
        }

        return (
            <ValidatorForm
                ref={node => (this.form = node)}
                onSubmit={this.handleSubmit}
                method="post"
            >
                {this.state.isLoading && <LoadingScreen showLoading={true}/>}

                <div className="Organizations">
                    {this.state.organizations.map((organization, index) => {
                    return <Row key={index} className="Organization RepeatedBlock">
                        <Col md={12}>
                            <FormHeading label={'Organization ' + (index + 1)} />
                            {index > 0 && <Button variant="link" className="RemoveButton" onClick={() => {this.removeOrganization(index)}}>Delete organization <Icon type="crossThick"/></Button>}
                        </Col>
                        <Col md={6}>
                            <FormItem label="Name">
                                <Input
                                    validators={['required']}
                                    errorMessages={[required]}
                                    name="name"
                                    onChange={(e) => {this.handleChange(index, e)}}
                                    onBlur={this.handleFieldVisit}
                                    value={organization.name}
                                    serverError={this.state.validation[index].name}
                                />
                            </FormItem>
                            <FormItem label="Department(s)">
                                <Input
                                    validators={['required']}
                                    errorMessages={[required]}
                                    name="department"
                                    onChange={(e) => {this.handleChange(index, e)}}
                                    onBlur={this.handleFieldVisit}
                                    value={organization.department}
                                    serverError={this.state.validation[index].department}
                                />
                            </FormItem>
                            <FormItem label="City">
                                <Input
                                    validators={['required']}
                                    errorMessages={[required]}
                                    name="city"
                                    onChange={(e) => {this.handleChange(index, e)}}
                                    onBlur={this.handleFieldVisit}
                                    value={organization.city}
                                    serverError={this.state.validation[index].city}
                                />
                            </FormItem>
                            <FormItem label="Country">
                                <Dropdown
                                    validators={['required']}
                                    errorMessages={[required]}
                                    options={this.state.countries}
                                    name="country"
                                    onChange={(e) => {this.handleCountryChange(index, e)}}
                                    onBlur={this.handleFieldVisit}
                                    value={this.state.countries.filter(({value}) => value === organization.country)}
                                    serverError={this.state.validation[index].country}
                                />
                            </FormItem>
                        </Col>
                        <Col md={6}>
                            <FormItem label="Additional Information">
                                <Input
                                    name="additionalInformation"
                                    onChange={(e) => {this.handleChange(index, e)}}
                                    onBlur={this.handleFieldVisit}
                                    value={organization.additionalInformation}
                                    serverError={this.state.validation[index].additionalInformation}
                                    as="textarea" rows="5"
                                />
                            </FormItem>
                        </Col>
                    </Row>;
                })}
                </div>
                <Row>
                    <Col md={12}>
                        <Button variant="link" onClick={this.handleNewOrganization}>+ Add Another Organisation</Button>
                    </Col>
                </Row>

                <Row className="FullScreenSteppedFormButtons">
                    <Col md={6}>
                        <LinkContainer to={'/my-studies/' + catalog + '/study/' + this.props.studyId + '/metadata/details'}>
                            <Button variant="secondary">Back</Button>
                        </LinkContainer>
                    </Col>
                    <Col md={6}>
                        <Button variant="primary" type="submit" disabled={this.state.submitDisabled}>Next</Button>
                    </Col>
                </Row>

            </ValidatorForm>
        );
    }
}
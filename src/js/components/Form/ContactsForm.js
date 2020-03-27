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
import FormHeading from "./FormHeading";
import LoadingScreen from "../LoadingScreen";
import Icon from "../Icon";

export default class ContactsForm extends Component {
    constructor(props) {
        super(props);

        this.state = {
            contacts: [
                {
                    firstName: '',
                    middleName: '',
                    lastName: '',
                    email: '',
                    orcid: ''
                }
            ],
            visitedFields: {},
            changedFieldsSinceFormSubmission: {},
            validation: [
                {
                    firstName: null,
                    middleName: null,
                    lastName: null,
                    email: null,
                    orcid: null
                }
            ],
            isSaved: false,
            submitDisabled: false,
            isLoading: false,
        };
    }

    handleNewContact = () => {
        this.form.isFormValid(false).then(valid => {
            if (valid) {
                this.setState({
                    contacts: [
                        ...this.state.contacts,
                        {
                            firstName: '',
                            middleName: '',
                            lastName: '',
                            email: '',
                            orcid: ''
                        }
                    ],
                    validation:    [
                        ...this.state.validation,
                        {
                            firstName: null,
                            middleName: null,
                            lastName: null,
                            email: null,
                            orcid: null
                        }
                    ]
                });
            }
        });
    };

    removeContact = (index) => {

        const { contacts, validation } = this.state;

        contacts.splice(index, 1);
        validation.splice(index, 1);

        this.setState({
            contacts: contacts,
            validation: validation
        });
    };

    getContacts = () => {
        this.setState({
            isLoading: true,
        });

        axios.get('/api/study/' + this.props.studyId + '/contacts')
            .then((response) => {
                if(response.data.length !== 0)
                {
                    let validation = [];
                    for (let i = 0; i < response.data.length; i++) {
                        validation.push({
                            firstName: null,
                            middleName: null,
                            lastName: null,
                            email: null,
                            orcid: null
                        });
                    }

                    this.setState({
                        contacts: response.data,
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
        ValidatorForm.addValidationRule('isOrcid', (value) => {
            if(value === '')
            {
                return true;
            }

            var regex = /^\d{4}(-)\d{4}(-)\d{4}(-)\d{4}$/i;
            return regex.test(value);
        });

        this.getContacts();
    }

    handleChange = (index, event, callback = (() => {})) => {
        const { contacts, validation } = this.state;

        contacts[index][event.target.name] = event.target.value;
        validation[index][event.target.name] = false;

        this.setState({
            contacts: contacts,
            validation: validation
        }, callback);
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

    createDataset = () => {
        axios.post('/api/catalog/' + this.props.catalog + '/study/' + this.props.studyId + '/publish', this.state.contacts)
            .then((response) => {
                this.setState({
                    isSaved: true
                });
            })
            .catch((error) => {
                this.setState({
                    submitDisabled: false,
                    isLoading: false
                });

                if(error.response && typeof error.response.data.error !== "undefined")
                {
                    toast.error(<ToastContent type="error" message={error.response.data.error} />);
                } else {
                    toast.error(<ToastContent type="error" message="An error occurred" />);
                }
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
            axios.post('/api/study/' + this.props.studyId + '/contacts/add', this.state.contacts)
                .then((response) => {
                   this.createDataset();
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
            return <Redirect push to={'/my-studies/' + catalog + '/study/' + studyId + '/metadata/finished'} />;
        }

        return (
            <ValidatorForm
                ref={node => (this.form = node)}
                onSubmit={this.handleSubmit}
                method="post"
            >
                {this.state.isLoading && <LoadingScreen showLoading={true}/>}

                <div className="Contacts">
                    {this.state.contacts.map((contact, index) => {
                    return <Row key={index} className="Contact RepeatedBlock">
                        <Col md={12}>
                            <FormHeading label={'Contact ' + (index + 1)} />
                            {index > 0 && <Button variant="link" className="RemoveButton" onClick={() => {this.removeContact(index)}}>Delete contact <Icon type="crossThick"/></Button>}
                        </Col>
                        <Col md={6}>
                            <FormItem label="First Name">
                                <Input
                                    validators={['required']}
                                    errorMessages={[required]}
                                    name="firstName"
                                    onChange={(e) => {this.handleChange(index, e)}}
                                    onBlur={this.handleFieldVisit}
                                    value={contact.firstName}
                                    serverError={this.state.validation[index].firstName}
                                />
                            </FormItem>
                            <FormItem label="Middle Name">
                                <Input
                                    name="middleName"
                                    onChange={(e) => {this.handleChange(index, e)}}
                                    onBlur={this.handleFieldVisit}
                                    value={contact.middleName}
                                    serverError={this.state.validation[index].middleName}
                                />
                            </FormItem>
                            <FormItem label="Last Name">
                                <Input
                                    validators={['required']}
                                    errorMessages={[required]}
                                    name="lastName"
                                    onChange={(e) => {this.handleChange(index, e)}}
                                    onBlur={this.handleFieldVisit}
                                    value={contact.lastName}
                                    serverError={this.state.validation[index].lastName}
                                />
                            </FormItem>
                            <FormItem label="Email address">
                                <Input
                                    validators={['required', 'isEmail']}
                                    errorMessages={[required, invalid]}
                                    name="email"
                                    onChange={(e) => {this.handleChange(index, e)}}
                                    onBlur={this.handleFieldVisit}
                                    value={contact.email}
                                    serverError={this.state.validation[index].email}
                                />
                            </FormItem>
                            <FormItem label="ORCID">
                                <Input
                                    placeholder="0000-0000-0000-0000"
                                    validators={['isOrcid']}
                                    errorMessages={[invalid]}
                                    name="orcid"
                                    onChange={(e) => {this.handleChange(index, e)}}
                                    onBlur={this.handleFieldVisit}
                                    value={contact.orcid}
                                    mask={[/\d/, /\d/, /\d/, /\d/, '-', /\d/, /\d/, /\d/, /\d/, '-', /\d/, /\d/, /\d/, /\d/, '-', /\d/, /\d/, /\d/, /\d/]}
                                    serverError={this.state.validation[index].orcid}
                                />
                            </FormItem>
                        </Col>
                        <Col md={6}>

                        </Col>
                    </Row>;
                })}
                </div>
                <Row>
                    <Col md={12}>
                        <Button variant="link" onClick={this.handleNewContact}>+ Add Another Contact</Button>
                    </Col>
                </Row>

                <Row className="FullScreenSteppedFormButtons">
                    <Col>
                        <LinkContainer to={'/my-studies/' + catalog + '/study/' + this.props.studyId + '/metadata/centers'}>
                            <Button variant="secondary">Back</Button>
                        </LinkContainer>
                    </Col>
                    <Col>
                        <Button variant="primary" type="submit" disabled={this.state.submitDisabled}>Finish</Button>
                    </Col>
                </Row>

            </ValidatorForm>
        );
    }
}
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
import InlineLoader from "../LoadingScreen/InlineLoader";
import Spinner from "react-bootstrap/Spinner";

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
            if(value === '' || value === null)
            {
                return true;
            }

            const regex = /^\d{4}(-)\d{4}(-)\d{4}(-)\d{3}[\dX]$/i;
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

    handleSubmit = (event) => {
        const { studyId, admin = false } = this.props;
        event.preventDefault();

        window.onbeforeunload = null;

        if(this.form.isFormValid()) {
            this.setState({
                submitDisabled: true,
                isLoading: true
            });

            axios.post('/api/study/' + studyId + '/contacts/add', this.state.contacts)
                .then((response) => {
                    this.setState({
                        isSaved: true,
                        isLoading: false,
                        submitDisabled: false,
                    });

                    if (admin) {
                        toast.success(<ToastContent type="success" message="The contacts are saved successfully" />, {
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
        const { catalog, studyId, admin = false } = this.props;
        const { isSaved, isLoading, submitDisabled } = this.state;

        const backUrl = '/my-studies/' + catalog + '/study/' + studyId + '/metadata/centers';
        const nextUrl = '/my-studies/' + catalog + '/study/' + studyId + '/metadata/consent';

        const required = "This field is required";
        const invalid = "This value is invalid";

        if(isSaved && !admin)
        {
            return <Redirect push to={nextUrl} />;
        }

        if(isLoading && !submitDisabled) {
            return <InlineLoader />;
        }

        return (
            <ValidatorForm
                ref={node => (this.form = node)}
                onSubmit={this.handleSubmit}
                method="post"
            >
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
                                    mask={[/\d/, /\d/, /\d/, /\d/, '-', /\d/, /\d/, /\d/, /\d/, '-', /\d/, /\d/, /\d/, /\d/, '-', /\d/, /\d/, /\d/, /[\dX]/]}
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
                        {!admin && <LinkContainer to={backUrl}>
                            <Button variant="secondary">Back</Button>
                        </LinkContainer>}
                    </Col>
                    <Col>
                        {admin ? <Button variant="primary" type="submit" disabled={this.state.submitDisabled}>
                            {isLoading && <Spinner as="span" animation="border" size="sm" role="status" aria-hidden="true" />}
                            Save
                        </Button> : <Button variant="primary" type="submit" disabled={this.state.submitDisabled}>
                            Next
                        </Button>}
                    </Col>
                </Row>

            </ValidatorForm>
        );
    }
}
import React, {Component} from 'react';
import {Button, Stack} from "@castoredc/matter";
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
import InlineLoader from "../LoadingScreen/InlineLoader";
import Toggle from "../Toggle";
import Container from "react-bootstrap/Container";
import {replaceAt} from "../../util";

export default class ContactsForm extends Component {
    constructor(props) {
        super(props);

        this.state = {
            contacts: [
                defaultData
            ],
            visitedFields: {},
            changedFieldsSinceFormSubmission: {},
            validation: [
                defaultValidation
            ],
            isSaved: false,
            submitDisabled: false,
            isLoading: false,
        };
    }

    handleNewContact = (e) => {
        const { contacts, validation } = this.state;
        e.preventDefault();

        this.form.isFormValid(false).then(valid => {
            if (valid) {
                this.setState({
                    contacts: [
                        ...contacts,
                        defaultData
                    ],
                    validation:    [
                        ...validation,
                        defaultValidation
                    ]
                });
            }
        });
    };

    removeContact = (e, index) => {
        const { contacts, validation } = this.state;

        e.preventDefault();

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
                        validation.push(defaultValidation);
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

        const newContacts = replaceAt(contacts, index, {
            ...contacts[index],
            [event.target.name]: event.target.value
        });

        const newValidation = replaceAt(validation, index, {
            ...validation[index],
            [event.target.name]: false
        });

        this.setState({
            contacts: newContacts,
            validation: newValidation
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
        const { contacts } = this.state;

        event.preventDefault();

        window.onbeforeunload = null;

        if(this.form.isFormValid()) {
            this.setState({
                submitDisabled: true,
                isLoading: true
            });

            axios.post('/api/study/' + studyId + '/contacts/add', contacts)
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
        const { contacts, validation, isSaved, isLoading, submitDisabled } = this.state;

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
                    {contacts.map((contact, index) => {
                        const name = [contact.firstName, contact.middleName, contact.lastName].filter(Boolean).join(' ');
                        const title = 'Contact ' + (index + 1) + (name.length > 0 ? ': ' + name : '');

                        return <Toggle key={index} title={title} expanded={contacts.length === (index + 1)}>
                            <Container>
                                <Row>
                                    <Col>
                                        <FormItem label="First Name">
                                            <Input
                                                validators={['required']}
                                                errorMessages={[required]}
                                                name="firstName"
                                                onChange={(e) => {this.handleChange(index, e)}}
                                                onBlur={this.handleFieldVisit}
                                                value={contact.firstName}
                                                serverError={validation[index].firstName}
                                            />
                                        </FormItem>
                                        <FormItem label="Middle Name">
                                            <Input
                                                name="middleName"
                                                onChange={(e) => {this.handleChange(index, e)}}
                                                onBlur={this.handleFieldVisit}
                                                value={contact.middleName}
                                                serverError={validation[index].middleName}
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
                                                serverError={validation[index].lastName}
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
                                                serverError={validation[index].email}
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
                                                serverError={validation[index].orcid}
                                            />
                                        </FormItem>
                                    </Col>
                                    <Col>
                                        {index > 0 && <Stack alignment="end" distribution="trailing">
                                            <Button buttonType="danger" className="RemoveButton" icon="cross" onClick={(e) => {this.removeContact(e, index)}}>Delete contact</Button>
                                        </Stack>}
                                    </Col>
                                </Row>
                            </Container>
                        </Toggle>;
                })}
                </div>
                <Row>
                    <Col md={12}>
                        <Button buttonType="secondary" icon="add" onClick={this.handleNewContact}>Add Another Contact</Button>
                    </Col>
                </Row>

                <Row className="FullScreenSteppedFormButtons">
                    <Col>
                        {!admin && <LinkContainer to={backUrl}>
                            <Button buttonType="secondary">Back</Button>
                        </LinkContainer>}
                    </Col>
                    <Col>
                        {admin ? <Button type="submit" disabled={this.state.submitDisabled}>
                            Save
                        </Button> : <Button type="submit" disabled={this.state.submitDisabled}>
                            Next
                        </Button>}
                    </Col>
                </Row>

            </ValidatorForm>
        );
    }
}

const defaultData = {
    firstName: '',
    middleName: '',
    lastName: '',
    email: '',
    orcid: ''
};

const defaultValidation = {
    firstName: null,
    middleName: null,
    lastName: null,
    email: null,
    orcid: null
};
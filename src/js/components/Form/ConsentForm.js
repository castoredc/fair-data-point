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
import LoadingScreen from "../LoadingScreen";
import RadioGroup from "../RadioGroup";

export default class ConsentForm extends Component {
    constructor(props) {
        super(props);

        this.state = {
            data: {
                publish: null,
                socialMedia: null
            },
            metadataSource: null,
            visitedFields: {},
            validation: {},
            isSaved: false,
            submitDisabled: true,
            isLoading: false
        };
    }

    componentDidMount() {
        this.getConsent();
    }

    getConsent = () => {
        this.setState({
            isLoading: true,
        });

        axios.get('/api/study/' + this.props.studyId + '/consent')
            .then((response) => {
                this.checkConsent(response.data.consent.publish, response.data.consent.socialMedia);

                this.setState({
                    data: response.data.consent,
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

    checkConsent = (publish, socialMedia) => {
        this.setState({
           submitDisabled: ! (publish !== null && socialMedia !== null)
        });
    };

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
        this.checkConsent(newState.data.publish, newState.data.socialMedia);
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
            axios.post('/api/catalog/' + this.props.catalog + '/study/' + this.props.studyId + '/consent', {
                publish:                   this.state.data.publish,
                socialMedia:               this.state.data.socialMedia,
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
        const { catalog, studyId, action, admin} = this.props;

        const backUrl = admin ? '/admin/' + catalog + '/study/' + this.props.studyId + '/metadata/' + action + '/contacts' : '/my-studies/' + catalog + '/study/' + this.props.studyId + '/metadata/contacts';
        const nextUrl = admin ? '/admin/' + catalog : '/my-studies/' + catalog + '/study/' + this.props.studyId + '/metadata/finished';

        const required = "This field is required";

        if(this.state.isSaved)
        {
            return <Redirect push to={nextUrl} />;
        }

        return (
            <ValidatorForm
                ref={node => (this.form = node)}
                onSubmit={this.handleSubmit}
                method="post"
            >
                {this.state.isLoading && <LoadingScreen showLoading={true}/>}

                <Col md={12}>
                    <FormItem label="Do you give permission to share high-level information about your study on a webpage to allow other researchers to find your project?">
                        <RadioGroup
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
                            onBlur={this.handleFieldVisit}
                            value={this.state.data.publish}
                            variant="horizontal"
                            name="publish"
                        />
                    </FormItem>
                    <FormItem label="Do you give permission to promote your project on Castor's social media channels to create more awareness for your project?">
                        <RadioGroup
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
                            onBlur={this.handleFieldVisit}
                            value={this.state.data.socialMedia}
                            variant="horizontal"
                            name="socialMedia"
                        />
                    </FormItem>
                </Col>

                <Row className="FullScreenSteppedFormButtons">
                    <Col>
                        <LinkContainer to={backUrl}>
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
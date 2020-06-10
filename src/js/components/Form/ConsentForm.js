import React, {Component} from 'react';
import {Button} from "@castoredc/matter";
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
import RadioGroup from "../Input/RadioGroup";
import InlineLoader from "../LoadingScreen/InlineLoader";

export default class ConsentForm extends Component {
    constructor(props) {
        super(props);

        this.state = {
            data: {
                publish: null,
                socialMedia: null
            },
            metadataSource: null,
            validation: {},
            isSaved: false,
            submitDisabled: true,
            isLoading: true
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
                this.checkConsent(response.data.publish, response.data.socialMedia);

                this.setState({
                    data: {
                        socialMedia: response.data.socialMedia,
                        publish: response.data.publish
                    },
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
        console.log(event.target.name);
        console.log(event.target.value);
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
        this.setState(newState, callback);
        this.checkConsent(newState.data.publish, newState.data.socialMedia);
    };

    handleSubmit = (event) => {
        const { catalog, studyId, admin = false } = this.props;
        const { data } = this.state;

        event.preventDefault();

        if(this.form.isFormValid()) {
            this.setState({
                submitDisabled: true,
                isLoading: true
            });

            axios.post('/api/catalog/' + catalog + '/study/' + studyId + '/consent', {
                publish:     data.publish,
                socialMedia: data.socialMedia,
            })
                .then(() => {
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
        const { data, isSaved, isLoading, submitDisabled } = this.state;

        const backUrl = '/my-studies/' + catalog + '/study/' + studyId + '/metadata/contacts';
        const nextUrl = '/my-studies/' + catalog + '/study/' + studyId + '/metadata/finished';

        if(isSaved && !admin)
        {
            return <Redirect push to={nextUrl} />;
        }

        if(isLoading && !isSaved) {
            return <InlineLoader />;
        }

        return (
            <ValidatorForm
                ref={node => (this.form = node)}
                onSubmit={this.handleSubmit}
                method="post"
            >
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
                            value={data.publish}
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
                            value={data.socialMedia}
                            variant="horizontal"
                            name="socialMedia"
                        />
                    </FormItem>
                </Col>

                <Row className="FullScreenSteppedFormButtons">
                    <Col>
                        {!admin && <LinkContainer to={backUrl}>
                            <Button buttonType="secondary">Back</Button>
                        </LinkContainer>}
                    </Col>
                    <Col>
                        {admin ? <Button type="submit" disabled={submitDisabled}>
                            Save
                        </Button> : <Button type="submit" disabled={submitDisabled}>
                            Finish
                        </Button>}
                    </Col>
                </Row>

            </ValidatorForm>
        );
    }
}
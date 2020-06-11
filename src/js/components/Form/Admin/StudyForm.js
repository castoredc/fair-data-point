import React, {Component} from 'react';
import Row from 'react-bootstrap/Row'
import Col from 'react-bootstrap/Col'
import {ValidatorForm} from 'react-form-validator-core';

import './../Form.scss'
import {Redirect} from "react-router-dom";
import {toast} from "react-toastify";
import ToastContent from "../../ToastContent";
import axios from "axios";
import FormItem from "./../FormItem";
import Input from "../../Input";
import {Button} from "@castoredc/matter";
import RadioGroup from "../../Input/RadioGroup";
import Dropdown from "../../Input/Dropdown";

export default class StudyForm extends Component {
    constructor(props) {
        super(props);

        this.state = {
            data: {
                id: '',
                name: '',
                source: 'castor',
                sourceServer: '',
                hasStudyId: '',
                catalog: props.catalog ? props.catalog.slug : null,
            },
            castorServers: [],
            study: null,
            validation: {},
            isSaved: false,
            submitDisabled: false
        };
    }

    componentDidMount() {
        this.getServers();
    }

    getServers = () => {
        axios.get('/api/castor/servers')
            .then((response) => {
                this.setState({
                    castorServers: response.data.map((server) => {
                        return {value: server.id, label: server.name};
                    }),
                });
            })
            .catch((error) => {
                toast.error(<ToastContent type="error" message="An error occurred while retrieving the Castor servers"/>);
            });
    };

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

    handleSubmit = (event) => {
        const { data } = this.state;

        event.preventDefault();

        this.setState({
            submitDisabled: true,
        });

        if(this.form.isFormValid()) {
            axios.post('/api/study', data)
                .then((response) => {
                    this.setState({
                        isSaved: true,
                        study: response.data,
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
                    });
                });
        }

        return false;
    };

    render() {
        const { data, validation, isSaved, submitDisabled, study, castorServers } = this.state;

        const required = "This field is required";

        if(isSaved)
        {
            return <Redirect push to={'/admin/study/' + study.id} />;
        }

        return (
            <ValidatorForm
                ref={node => (this.form = node)}
                onSubmit={this.handleSubmit}
                method="post"
            >
            <FormItem label="Source">
                <RadioGroup
                    options={[
                        {
                            label: 'Castor EDC',
                            value: 'castor'
                        },
                    ]}
                    onChange={this.handleChange}
                    value={data.source}
                    name="source"
                />
            </FormItem>

            <FormItem label="Name">
                <Input
                    validators={['required']}
                    errorMessages={[required]}
                    name="name"
                    onChange={this.handleChange}
                    value={data.name}
                    serverError={validation.name}
                />
            </FormItem>
            {data.source === 'castor' && <div>
                <FormItem label="Server">
                    <Dropdown
                        validators={['required']}
                        errorMessages={[required]}
                        options={castorServers}
                        name="sourceServer"
                        onChange={(e) => {this.handleChange({target: { name: 'sourceServer', value: e.value }})}}
                        value={castorServers.filter(({value}) => value === data.sourceServer)}
                        serverError={validation.order}
                    />
                </FormItem>

                <FormItem label="Is the Study ID known?">
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
                        value={data.hasStudyId}
                        variant="horizontal"
                        name="hasStudyId"
                    />
                </FormItem>

                {data.hasStudyId && <FormItem label="Study ID">
                    <Input
                        validators={['required']}
                        errorMessages={[required]}
                        name="id"
                        onChange={this.handleChange}
                        value={data.id}
                        serverError={validation.id}
                    />
                </FormItem>}

            </div>}

            <Row className="FullScreenSteppedFormButtons">
                <Col>
                </Col>
                <Col>
                    <Button variant="primary" type="submit" disabled={submitDisabled}>
                        Add study
                    </Button>
                </Col>
            </Row>

            </ValidatorForm>
        );
    }
}
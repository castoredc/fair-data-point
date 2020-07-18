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
import {Button, Stack} from "@castoredc/matter";
import Modal from "../../../modals/Modal";

export default class StudyForm extends Component {
    constructor(props) {
        super(props);

        let data = props.study ? mergeData(defaultData, props.study) : defaultData;
        data['hasStudyId'] = data.sourceId !== '' ? true : '';

        this.state = {
            data:          data,
            validation:    {},
            isSaved:       false,
            isLoading:     false,
            castorServers: [],
            study:         null,
            catalog:       props.catalog ? props.catalog : null,
            update:        !!props.study,
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
                toast.error(<ToastContent type="error"
                                          message="An error occurred while retrieving the Castor servers"/>);
            });
    };

    handleChange = (event) => {
        const {data} = this.state;

        let newData = {
            ...data,
            [event.target.name]: event.target.value,
        };

        if (event.target.name === 'hasStudyId') {
            newData.sourceId = '';
        }

        const newState = {
            data:       newData,
            validation: {
                [event.target.name]: false,
            },
        };
        this.setState(newState);
    };

    handleSubmit = (event) => {
        const {data, catalog} = this.state;
        const {study} = this.props;

        event.preventDefault();

        this.setState({
            isLoading: true,
        });

        const newData = {
            ...data,
            catalog: catalog,
        };

        if (this.form.isFormValid()) {
            axios.post('/api/study' + (study ? '/' + study.id : ''), newData)
                .then((response) => {
                    this.setState({
                        isSaved:   true,
                        study:     response.data,
                        isLoading: false,
                    });

                    if (study) {
                        toast.success(<ToastContent type="success"
                                                    message="The study details are saved successfully"/>, {
                            position: "top-right",
                        });
                    }
                })
                .catch((error) => {
                    if (error.response && error.response.status === 400) {
                        this.setState({
                            validation: error.response.data.fields,
                        });
                    } else {
                        toast.error(<ToastContent type="error" message="An error occurred"/>, {
                            position: "top-center",
                        });
                    }
                    this.setState({
                        isLoading: false,
                    });
                });
        }

        return false;
    };

    render() {
        const {data, validation, castorServers, isSaved, update, study, isLoading} = this.state;

        const required = "This field is required";

        if (isSaved && !update) {
            return <Redirect push to={'/admin/study/' + study.id + '/metadata'}/>;
        }

        return (
            <ValidatorForm
                className="FullHeightForm"
                ref={node => (this.form = node)}
                onSubmit={this.handleSubmit}
                method="post"
            >
                <div className="FormContent">
                    <FormItem label="Source">
                        <RadioGroup
                            options={[
                                {
                                    label: 'Castor EDC',
                                    value: 'castor',
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
                                onChange={(e) => {
                                    this.handleChange({target: {name: 'sourceServer', value: e.value}})
                                }}
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
                                        value: true,
                                    },
                                    {
                                        label: 'No',
                                        value: false,
                                    },
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
                                name="sourceId"
                                onChange={this.handleChange}
                                value={data.sourceId}
                                serverError={validation.sourceId}
                            />
                        </FormItem>}
                    </div>}

                    {update && <FormItem label="Publish study">
                        <RadioGroup
                            validators={['required']}
                            errorMessages={[required]}
                            options={[
                                {
                                    label: 'Yes',
                                    value: true,
                                },
                                {
                                    label: 'No',
                                    value: false,
                                },
                            ]}
                            onChange={this.handleChange}
                            value={data.published}
                            variant="horizontal"
                            name="published"
                        />
                    </FormItem>}
                </div>

                {update ? <div className="FormButtons">
                    <Stack distribution="trailing">
                        <Button type="submit" disabled={isLoading}>
                            Update study
                        </Button>
                    </Stack>
                </div> : <footer>
                    <Button type="submit" disabled={isLoading}>
                        Add study
                    </Button>
                </footer>}

            </ValidatorForm>
        );
    }
}

export const defaultData = {
    sourceId:     '',
    name:         '',
    source:       'castor',
    sourceServer: '',
    published:    false,
    slug:         '',
};

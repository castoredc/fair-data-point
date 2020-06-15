import React, {Component} from 'react'
import {ValidatorForm} from "react-form-validator-core";
import FormItem from "../../components/Form/FormItem";
import Input from "../../components/Input";
import Dropdown from "../../components/Input/Dropdown";
import axios from "axios";
import {toast} from "react-toastify";
import ToastContent from "../../components/ToastContent";
import {Button} from "@castoredc/matter";
import RadioGroup from "../../components/Input/RadioGroup";
import Modal from "../Modal";
import {Redirect} from "react-router-dom";

export default class AddStudyModal extends Component {
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
            isLoading: false
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

    handleSubmit = () => {
        const { data } = this.state;

        this.setState({
            isLoading: true,
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
                        isLoading: false,
                    });
                });
        }

        return false;
    };

    render() {
        const { show, handleClose } = this.props;
        const { data, validation, isSaved, isLoading, study, castorServers } = this.state;


        const required = "This field is required";

        if(isSaved)
        {
            return <Redirect push to={'/admin/study/' + study.id} />;
        }

        return <Modal
            show={show}
            handleClose={handleClose}
            className="TripleModal"
            title="Add new study"
            closeButton
            footer={(
                <Button type="submit" disabled={isLoading} onClick={() => this.form.submit()}>
                    Add study
                </Button>
            )}
        >
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
            </ValidatorForm>
        </Modal>
    }
}
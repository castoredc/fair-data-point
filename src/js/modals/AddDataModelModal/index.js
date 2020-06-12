import React, {Component} from 'react'
import Modal from "react-bootstrap/Modal";
import {ValidatorForm} from "react-form-validator-core";
import axios from "axios";
import {toast} from "react-toastify";
import ToastContent from "../../components/ToastContent";
import FormItem from "../../components/Form/FormItem";
import Input from "../../components/Input";
import {Redirect} from "react-router-dom";
import {Button} from "@castoredc/matter";

export default class AddDataModelModal extends Component {
    constructor(props) {
        super(props);

        this.state = {
            data: {
                title: '',
                description: '',
            },
            isSaved: false,
            modelId: null,
            isLoading: false,
            validation: {},
        };
    }

    handleChange = (event, callback = (() => {
    })) => {
        const {data} = this.state;
        this.setState({
            data:       {
                ...data,
                [event.target.name]: event.target.value,
            },
            validation: {
                [event.target.name]: false,
            },
        }, callback);
    };

    handleSubmit = (event) => {
        const {data} = this.state;
        event.preventDefault();

        if (this.form.isFormValid()) {
            this.setState({isLoading: true});

            axios.post('/api/model', data)
                .then((response) => {
                    this.setState({
                        isLoading: false,
                        isSaved: true,
                        modelId: response.data.id
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
                    this.setState({isLoading: false});
                });
        }
    };

    render() {
        const {show, handleClose} = this.props;
        const {data, isLoading, isSaved, modelId } = this.state;

        const required = "This field is required";

        if(isSaved)
        {
            return <Redirect push to={'/admin/model/' + modelId} />;
        }

        return <Modal show={show} onHide={handleClose} className="AddDataModelModal">
            <ValidatorForm
                ref={node => (this.form = node)}
                onSubmit={this.handleSubmit}
                method="post"
            >
                <Modal.Header closeButton>
                    <Modal.Title>New Data Model</Modal.Title>
                </Modal.Header>
                <Modal.Body>
                    <FormItem label="Title">
                        <Input
                            validators={['required']}
                            errorMessages={[required]}
                            name="title"
                            onChange={this.handleChange}
                            value={data.title}
                            serverError={this.state.validation.title}
                        />
                    </FormItem>
                    <FormItem label="Description">
                        <Input
                            name="description"
                            onChange={this.handleChange}
                            value={data.description}
                            serverError={this.state.validation.description}
                            as="textarea" rows="5"
                        />
                    </FormItem>
                </Modal.Body>
                <Modal.Footer>
                    <Button type="submit" disabled={isLoading}>
                        Create model
                    </Button>
                </Modal.Footer>
            </ValidatorForm>
        </Modal>
    }
}
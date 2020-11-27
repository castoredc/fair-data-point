import React, {Component} from 'react'
import {ValidatorForm} from "react-form-validator-core";
import axios from "axios";
import {toast} from "react-toastify";
import ToastContent from "../../components/ToastContent";
import FormItem from "../../components/Form/FormItem";
import Input from "../../components/Input";
import {Redirect} from "react-router-dom";
import {Button} from "@castoredc/matter";
import Modal from "../Modal";

export default class AddDataDictionaryModal extends Component {
    constructor(props) {
        super(props);

        this.state = {
            data: {
                title: '',
                description: '',
            },
            isSaved: false,
            dictionaryId: null,
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

    handleSubmit = () => {
        const {data} = this.state;

        if (this.form.isFormValid()) {
            this.setState({isLoading: true});

            axios.post('/api/dictionary', data)
                .then((response) => {
                    this.setState({
                        isLoading: false,
                        isSaved: true,
                        dictionaryId: response.data.id
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
        const {data, isLoading, isSaved, dictionaryId } = this.state;

        const required = "This field is required";

        if(isSaved)
        {
            return <Redirect push to={'/admin/dictionary/' + dictionaryId} />;
        }

        return <Modal
            show={show}
            handleClose={handleClose}
            className="AddDataDictionaryModal"
            title="New Data Dictionary"
            closeButton
            footer={(
                <Button type="submit" disabled={isLoading} onClick={() => this.form.submit()}>
                    Create dictionary
                </Button>
            )}
        >
            <ValidatorForm
                ref={node => (this.form = node)}
                onSubmit={this.handleSubmit}
                method="post"
            >
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
            </ValidatorForm>
        </Modal>
    }
}
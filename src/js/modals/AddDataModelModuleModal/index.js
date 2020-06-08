import React, {Component} from 'react'
import Modal from "react-bootstrap/Modal";
import {ValidatorForm} from "react-form-validator-core";
import Input from "../../components/Input";
import FormItem from "../../components/Form/FormItem";
import Dropdown from "../../components/Input/Dropdown";
import axios from "axios";
import {toast} from "react-toastify";
import ToastContent from "../../components/ToastContent";
import Spinner from "react-bootstrap/Spinner";
import {Button} from "@castoredc/matter";

export default class AddDataModelModuleModal extends Component {
    constructor(props) {
        super(props);

        this.state = {
            data: {
                title: '',
                order: (props.orderOptions.length === 1) ? props.orderOptions[0].value : null,
            },
            validation: {},
            isSaved: false,
            submitDisabled: false,
            isLoading: false
        };
    }

    handleChange = (event, callback = (() => {})) => {
        const { data } = this.state;
        this.setState({
            data: {
                ...data,
                [event.target.name]: event.target.value,
            },
            validation: {
                [event.target.name]: false,
            }
        }, callback);
    };

    handleSelectChange = (name, event) => {
        this.handleChange({
            target: {
                name: name,
                value: event.value
            }
        });
    };

    handleSubmit = (event) => {
        const {data} = this.state;
        const {modelId, onSaved} = this.props;
        event.preventDefault();

        if (this.form.isFormValid()) {
            this.setState({
                submitDisabled: true,
                isLoading:      true
            });

            axios.post('/api/model/' + modelId + '/module/add', data)
                .then(() => {
                    this.setState({
                        isSaved: true,
                        isLoading: false,
                        submitDisabled: false,
                    });

                    onSaved();
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
    };

    render() {
        const { show, handleClose, orderOptions } = this.props;
        const {data, validation, isLoading} = this.state;

        const required = "This field is required";

        return <Modal show={show} onHide={handleClose}>
            <ValidatorForm
                ref={node => (this.form = node)}
                onSubmit={this.handleSubmit}
                method="post"
            >
                <Modal.Header closeButton>
                    <Modal.Title>Add module</Modal.Title>
                </Modal.Header>
                <Modal.Body>
                        <FormItem label="Title">
                            <Input
                                validators={['required']}
                                errorMessages={[required]}
                                name="title"
                                onChange={this.handleChange}
                                value={data.title}
                                serverError={validation.title}
                            />
                        </FormItem>

                        <FormItem label="Position">
                            <Dropdown
                                validators={['required']}
                                errorMessages={[required]}
                                options={orderOptions}
                                name="order"
                                onChange={(e) => {this.handleSelectChange('order', e)}}
                                onBlur={this.handleFieldVisit}
                                value={orderOptions.filter(({value}) => value === data.order)}
                                serverError={validation.order}
                            />
                        </FormItem>
                </Modal.Body>
                <Modal.Footer>
                    <Button type="submit" disabled={isLoading}>
                        Add module
                    </Button>
                </Modal.Footer>
            </ValidatorForm>
        </Modal>
    }
}
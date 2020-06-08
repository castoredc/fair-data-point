import React, {Component} from 'react'
import Modal from "react-bootstrap/Modal";
import {ValidatorForm} from "react-form-validator-core";
import Input from "../../components/Input";
import FormItem from "../../components/Form/FormItem";
import Dropdown from "../../components/Input/Dropdown";
import axios from "axios";
import {toast} from "react-toastify";
import ToastContent from "../../components/ToastContent";
import {Button} from "@castoredc/matter";
import ConfirmModal from "../ConfirmModal";
import {classNames} from "../../util";
import Container from "react-bootstrap/Container";

export default class DataModelModuleModal extends Component {
    constructor(props) {
        super(props);

        this.state = {
            data: this.handleNewData(),
            validation: {},
            isSaved: false,
            submitDisabled: false,
            isLoading: false
        };
    }

    componentDidUpdate(prevProps, prevState, snapshot) {
        const { show, data } = this.props;

        if (show !== prevProps.show || data !== prevProps.data) {
            this.setState({
                data: this.handleNewData()
            });
        }
    }

    handleNewData = () => {
        const { data, orderOptions } = this.props;

        let newData = defaultData;

        if(data !== null) {
            newData = data;
        } else {
            newData.order = orderOptions.slice(-1)[0].value;
        }

        return newData;
    };

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

            axios.post('/api/model/' + modelId + '/module' + (data.id ? '/' + data.id : ''), {
                title: data.title,
                order: data.order,
            })
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

    handleDelete = (callback) => {
        const {data} = this.state;
        const {modelId, onSaved} = this.props;

        if (data.id) {
            axios.delete('/api/model/' + modelId + '/module/' + data.id)
                .then(() => {
                    callback();
                    onSaved();
                })
                .catch((error) => {
                    toast.error(<ToastContent type="error" message="An error occurred"/>, {
                        position: "top-center"
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
                    {data.id ? <Modal.Title>Edit module</Modal.Title> : <Modal.Title>Add module</Modal.Title>}
                </Modal.Header>
                <Modal.Body>
                    <Container>
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
                    </Container>
                </Modal.Body>
                <Modal.Footer className={classNames(data.id && 'HasConfirmButton')}>
                    {data.id && <ConfirmModal
                        title="Delete module"
                        action="Delete module"
                        variant="danger"
                        onConfirm={this.handleDelete}
                        includeButton={true}
                        >
                        Are you sure you want to delete module <strong>{data.title}</strong>?<br />
                        This will also delete all associated triples.
                    </ConfirmModal>}
                    <Button type="submit" disabled={isLoading}>
                        {data.id ? 'Edit module' : 'Add module'}
                    </Button>
                </Modal.Footer>
            </ValidatorForm>
        </Modal>
    }
}

const defaultData = {
    title: '',
    order: '',
};
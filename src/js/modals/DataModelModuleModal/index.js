import React, {Component} from 'react'
import {ValidatorForm} from "react-form-validator-core";
import Input from "../../components/Input";
import FormItem from "../../components/Form/FormItem";
import Dropdown from "../../components/Input/Dropdown";
import axios from "axios";
import {toast} from "react-toastify";
import ToastContent from "../../components/ToastContent";
import {Button, Stack} from "@castoredc/matter";
import ConfirmModal from "../ConfirmModal";
import {classNames} from "../../util";
import Modal from "../Modal";
import RadioGroup from "../../components/Input/RadioGroup";
import DependencyModal from "../DependencyModal";

export default class DataModelModuleModal extends Component {
    constructor(props) {
        super(props);

        this.state = {
            data: this.handleNewData(),
            validation: {},
            isSaved: false,
            submitDisabled: false,
            isLoading: false,
            showDependencyModal: false,
        };
    }

    componentDidUpdate(prevProps, prevState, snapshot) {
        const {show, data} = this.props;

        if (show !== prevProps.show || data !== prevProps.data) {
            this.setState({
                data: this.handleNewData(),
            });
        }
    }

    handleNewData = () => {
        const {data, orderOptions} = this.props;

        let newData = defaultData;

        if (data !== null) {
            newData = data;
        } else {
            newData.order = orderOptions.slice(-1)[0].value;
        }

        return newData;
    };

    handleChange = (event) => {
        const {data} = this.state;
        this.setState({
            data: {
                ...data,
                [event.target.name]: event.target.value,
            },
            validation: {
                [event.target.name]: false,
            },
        });
    };

    handleSubmit = () => {
        const {data} = this.state;
        const {modelId, versionId, onSaved} = this.props;

        if (this.form.isFormValid()) {
            this.setState({
                submitDisabled: true,
                isLoading: true,
            });

            axios.post('/api/model/' + modelId + '/v/' + versionId + '/module' + (data.id ? '/' + data.id : ''), data)
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
                            validation: error.response.data.fields,
                        });
                    } else {
                        toast.error(<ToastContent type="error" message="An error occurred"/>, {
                            position: "top-center",
                        });
                    }
                    this.setState({
                        submitDisabled: false,
                        isLoading: false,
                    });
                });
        }
    };

    handleDelete = (callback) => {
        const {data} = this.state;
        const {modelId, versionId, onSaved} = this.props;

        if (data.id) {
            axios.delete('/api/model/' + modelId + '/v/' + versionId + '/module/' + data.id)
                .then(() => {
                    callback();
                    onSaved();
                })
                .catch((error) => {
                    toast.error(<ToastContent type="error" message="An error occurred"/>, {
                        position: "top-center",
                    });
                });
        }
    };

    openDependencyModal = () => {
        this.setState({
            showDependencyModal: true,
        });
    };

    closeDependencyModal = () => {
        this.setState({
            showDependencyModal: false,
        });
    };

    handleDependenciesChange = (dependencies) => {
        const {data} = this.state;

        this.setState({
            data: {
                ...data,
                dependencies: dependencies,
            },
            showDependencyModal: false,
        });
    };

    render() {
        const {show, handleClose, orderOptions, valueNodes, prefixes} = this.props;
        const {data, validation, isLoading, showDependencyModal} = this.state;

        const required = "This field is required";

        return <Modal
            show={show}
            handleClose={handleClose}
            title={data.id ? 'Edit group' : 'Add group'}
            closeButton
            className={classNames('DataModelModuleFormModal', data.dependent && 'ShowDependencyEditor')}
            footer={(
                <div className={classNames(data.id && 'HasConfirmButton')}>
                    <Stack
                        alignment="normal"
                        distribution="equalSpacing"
                    >
                        {data.id && <ConfirmModal
                            title="Delete group"
                            action="Delete group"
                            variant="danger"
                            onConfirm={this.handleDelete}
                            includeButton={true}
                        >
                            Are you sure you want to delete group <strong>{data.title}</strong>?<br/>
                            This will also delete all associated triples.
                        </ConfirmModal>}
                        <Button type="submit" disabled={isLoading} onClick={() => this.form.submit()}>
                            {data.id ? 'Edit group' : 'Add group'}
                        </Button>
                    </Stack>
                </div>
            )}
        >
            <DependencyModal
                show={showDependencyModal}
                handleClose={this.closeDependencyModal}
                save={this.handleDependenciesChange}
                valueNodes={valueNodes}
                prefixes={prefixes}
                dependencies={data.dependencies}
            />

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
                        serverError={validation.title}
                    />
                </FormItem>

                <FormItem label="Position">
                    <Dropdown
                        validators={['required']}
                        errorMessages={[required]}
                        options={orderOptions}
                        name="order"
                        onChange={(e) => {
                            this.handleChange({target: {name: 'order', value: e.value}})
                        }}
                        onBlur={this.handleFieldVisit}
                        value={orderOptions.filter(({value}) => value === data.order)}
                        serverError={validation.order}
                        menuPosition="fixed"
                    />
                </FormItem>

                <FormItem label="Repeated">
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
                        value={data.repeated}
                        variant="horizontal"
                        name="repeated"
                        serverError={validation.repeated}
                    />
                </FormItem>

                <FormItem label="Dependent">
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
                        value={data.dependent}
                        variant="horizontal"
                        name="dependent"
                        serverError={validation.dependent}
                    />
                </FormItem>

                {data.dependent && <FormItem>
                    <Button buttonType="secondary" onClick={this.openDependencyModal} icon="decision">Edit
                        dependencies</Button>
                </FormItem>}
            </ValidatorForm>
        </Modal>
    }
}

const defaultData = {
    title: '',
    order: '',
    repeated: false,
    dependent: false,
    dependencies: {
        rules: [],
        combinator: 'and',
        not: false
    },
};
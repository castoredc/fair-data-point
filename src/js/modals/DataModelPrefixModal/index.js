import React, {Component} from 'react'
import {ValidatorForm} from "react-form-validator-core";
import Input from "../../components/Input";
import FormItem from "../../components/Form/FormItem";
import axios from "axios";
import {toast} from "react-toastify";
import ToastContent from "../../components/ToastContent";
import {Button} from "@castoredc/matter";
import Modal from "../Modal";

export default class DataModelPrefixModal extends Component {
    constructor(props) {
        super(props);

        this.state = {
            data: props.data ? props.data : defaultData,
            validation: {},
            isSaved: false,
            submitDisabled: false,
            isLoading: false
        };
    }

    componentDidUpdate(prevProps, prevState, snapshot) {
        const {show, data} = this.props;

        if (show !== prevProps.show || data !== prevProps.data) {
            this.setState({
                data: data ? data : defaultData,
            })
        }
    }

    componentDidMount() {
        ValidatorForm.addValidationRule('isUrl', (value) => {
            var pattern = new RegExp('^((ft|htt)ps?:\\/\\/)?' + // protocol
                '((([a-z\\d]([a-z\\d-]*[a-z\\d])*)\\.)+[a-z]{2,}|' + // domain name and extension
                '((\\d{1,3}\\.){3}\\d{1,3}))' + // OR ip (v4) address
                '(\\:\\d+)?' + // port
                '(\\/[-a-z\\d%@_.~+&:]*)*' + // path
                '(\\?[;&a-z\\d%@_.,~+&:=-]*)?' + // query string
                '(\\#[-a-z\\d_]*)?$', 'i'); // fragment locator
            return pattern.test(value);
        });
    }

    handleChange = (event, callback = (() => {
    })) => {
        const {data} = this.state;
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

    handleSubmit = () => {
        const {modelId, versionId, onSaved} = this.props;
        const {data} = this.state;

        if (this.form.isFormValid()) {
            this.setState({
                submitDisabled: true,
                isLoading: true
            });

            axios.post('/api/model/' + modelId + '/v/' + versionId + '/prefix' + (data.id ? '/' + data.id : ''), {
                prefix: data.prefix,
                uri: data.uri,
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

    render() {
        const {show, handleClose} = this.props;
        const {data, validation, isLoading} = this.state;

        const required = "This field is required";
        const validUrl = "Please enter a valid URI";

        return <Modal
            show={show}
            handleClose={handleClose}
            title={data.id ? 'Edit prefix' : 'Add prefix'}
            closeButton
            footer={(
                <Button type="submit" disabled={isLoading} onClick={() => this.form.submit()}>
                    {data.id ? 'Edit prefix' : 'Add prefix'}
                </Button>
            )}
        >
            <ValidatorForm
                ref={node => (this.form = node)}
                onSubmit={this.handleSubmit}
                method="post"
            >
                <FormItem label="Prefix">
                    <Input
                        validators={['required']}
                        errorMessages={[required]}
                        name="prefix"
                        onChange={this.handleChange}
                        value={data.prefix}
                        serverError={validation.prefix}
                    />
                </FormItem>

                <FormItem label="URI">
                    <Input
                        validators={['required', 'isUrl']}
                        errorMessages={[required, validUrl]}
                        name="uri"
                        onChange={this.handleChange}
                        value={data.uri}
                        serverError={validation.uri}
                    />
                </FormItem>
            </ValidatorForm>
        </Modal>
    }
}

const defaultData = {
    prefix: '',
    uri: ''
};
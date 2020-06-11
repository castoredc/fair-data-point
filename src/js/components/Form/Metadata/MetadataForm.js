import React, {Component} from 'react';
import {toast} from "react-toastify/index";
import ToastContent from "../../ToastContent";
import axios from "axios/index";
import FormItem from "../FormItem";
import Dropdown from "../../Input/Dropdown";
import LocalizedTextInput from "../../Input/LocalizedTextInput";
import {ValidatorForm} from "react-form-validator-core";
import MetadataVersionModal from "../../../modals/MetadataVersionModal";
import Row from "react-bootstrap/Row";
import Col from "react-bootstrap/Col";
import {Button} from "@castoredc/matter";
import {mergeData} from "../../../util";

export default class MetadataForm extends Component {
    constructor(props) {
        super(props);

        const extendedDefaultData = props.defaultData ? props.defaultData : {};

        const mergedDefaultMetadata = {
            ...defaultData,
            ...extendedDefaultData
        };

        this.state = {
            data: props.object.hasMetadata ? mergeData(mergedDefaultMetadata, props.object.metadata) : mergedDefaultMetadata,
            currentVersion : props.object.hasMetadata ? props.object.metadata.version.metadata : null,
            validation: {},
            isSaved: false,
            submitDisabled: false,
            isLoading: false,
            showModal: false,
            languages: [],
            licenses: []
        };
    }

    componentDidMount() {
        this.getLanguages();
        this.getLicenses();
    }

    getLanguages = () => {
        axios.get('/api/languages')
            .then((response) => {
                this.setState({
                    languages: response.data,
                });
            })
            .catch((error) => {
                toast.error(<ToastContent type="error" message="An error occurred" />);
            });
    };

    getLicenses = () => {
        axios.get('/api/licenses')
            .then((response) => {
                this.setState({
                    licenses: response.data,
                });
            })
            .catch(() => {
                toast.error(<ToastContent type="error" message="An error occurred" />);
            });
    };

    openModal = () => {
        this.setState({
            showModal: true
        });
    };

    closeModal = () => {
        this.setState({
            showModal: false
        });
    };

    handleChange = (event) => {
        const { data } = this.state;

        this.setState({
            data: {
                ...data,
                [event.target.name]: event.target.value,
            },
            validation: {
                [event.target.name]: false,
            }
        });
    };

    handleVersionUpdate = (versionType) => {
        this.closeModal();

        const { data } = this.state;

        this.setState({
            data: {
                ...data,
                versionUpdate: versionType
            }
        }, () => {
            this.submitMetadata();
        });
    };

    handleSubmit = (event) => {
        event.preventDefault();

        if(this.form.isFormValid()) {
            const { currentVersion } = this.state;

            if(currentVersion === null) {
                this.handleVersionUpdate('major');
            } else {
                this.openModal();
            }
        }
    };

    submitMetadata = () => {
        const { object, type, onSave } = this.props;
        const { data } = this.state;

        if(this.form.isFormValid()) {
            this.setState({
                submitDisabled: true,
                isLoading: true
            });

            axios.post('/api/metadata/' + type + '/' + object.id, data)
                .then((response) => {
                    this.setState({
                        isSaved: true,
                        isLoading: false,
                        submitDisabled: false,
                    });

                    toast.success(<ToastContent type="success" message="The metadata are saved successfully" />, {
                        position: "top-right"
                    });

                    onSave();
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
        const { data, validation, languages, licenses, submitDisabled, currentVersion, showModal } = this.state;
        const { children } = this.props;

        const required = "This field is required";

        return (
            <ValidatorForm
                ref={node => (this.form = node)}
                onSubmit={this.handleSubmit}
                method="post"
            >
                <MetadataVersionModal
                    show={showModal}
                    currentVersion={currentVersion}
                    handleClose={this.closeModal}
                    onSave={this.handleVersionUpdate}
                />
                <FormItem label="Title">
                    <LocalizedTextInput
                        validators={['required']}
                        errorMessages={[required]}
                        name="title"
                        onChange={this.handleChange}
                        value={data.title}
                        serverError={validation.title}
                        languages={languages}
                    />
                </FormItem>
                <FormItem label="Description">
                    <LocalizedTextInput
                        validators={['required']}
                        errorMessages={[required]}
                        name="description"
                        onChange={this.handleChange}
                        value={data.description}
                        serverError={validation.description}
                        languages={languages}
                        as="textarea"
                        rows="8"
                    />
                </FormItem>

                <FormItem label="Language">
                    <Dropdown
                        validators={['required']}
                        errorMessages={[required]}
                        options={languages}
                        name="language"
                        onChange={(e) => {this.handleChange({target: { name: 'language', value: e.value }})}}
                        value={languages.filter(({value}) => value === data.language)}
                        serverError={validation.language}
                    />
                </FormItem>

                <FormItem label="License">
                    <Dropdown
                        options={licenses}
                        name="license"
                        onChange={(e) => {this.handleChange({target: { name: 'license', value: e.value }})}}
                        value={licenses.filter(({value}) => value === data.license)}
                        serverError={validation.license}
                    />
                </FormItem>

                {children && children(this.handleChange, data, validation)}

                <Row className="FullScreenSteppedFormButtons">
                    <Col />
                    <Col>
                        <Button type="submit" disabled={submitDisabled}>
                            Save
                        </Button>
                    </Col>
                </Row>
            </ValidatorForm>
        );
    }
}

const defaultData = {
    'title': null,
    'description': null,
    'language': null,
    'license': null,
    'versionUpdate': ''
};
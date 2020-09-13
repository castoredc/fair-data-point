import React, {Component} from 'react'
import {ValidatorForm} from "react-form-validator-core";
import FormItem from "../../components/Form/FormItem";
import FormHeading from "../../components/Form/FormHeading";
import Input from "../../components/Input";
import Dropdown from "../../components/Input/Dropdown";
import axios from "axios";
import {toast} from "react-toastify";
import ToastContent from "../../components/ToastContent";
import {Button, Stack} from "@castoredc/matter";
import Modal from "../Modal";

export default class TripleModal extends Component {
    constructor(props) {
        super(props);

        this.state = {
            data: props.data ? props.data : defaultData,
            validation: {},
            showFieldSelector: false,
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
        const {data, validation} = this.state;
        this.setState({
            data: {
                ...data,
                [event.target.name]: event.target.value,
            },
            validation: {
                ...validation,
                [event.target.name]: false,
            }
        }, callback);
    };

    handlePredicateChange = (event) => {
        const {data, validation} = this.state;
        const {prefixes} = this.props;

        let predicate = event.target.value;
        const regex = /^([^:]*):(.*)/;
        const matches = regex.exec(predicate);

        if (matches !== null) {
            const matchedPrefix = matches[1];
            const foundPrefix = prefixes.find((prefix) => {
                return prefix.prefix === matchedPrefix
            });

            if (typeof foundPrefix !== 'undefined') {
                predicate = foundPrefix.uri + matches[2];
            }
        }

        this.setState({
            data: {
                ...data,
                predicateValue: predicate,
            },
            validation: {
                ...validation,
                predicateValue: false,
            }
        });
    };

    handleSubjectTypeChange = (event) => {
        const {data} = this.state;

        this.setState({
            data: {
                ...data,
                subjectType: event.value,
                subjectValue: ''
            }
        });
    };

    handleSubjectValueChange = (event) => {
        const {data} = this.state;

        this.setState({
            data: {
                ...data,
                subjectValue: event.value
            }
        });
    };

    handleObjectTypeChange = (event) => {
        const {data} = this.state;

        this.setState({
            data: {
                ...data,
                objectType: event.value,
                objectValue: ''
            }
        });
    };

    handleObjectValueChange = (event) => {
        const {data} = this.state;

        this.setState({
            data: {
                ...data,
                objectValue: event.value
            }
        });
    };

    handleSubmit = () => {
        const {modelId, versionId, module, onSaved} = this.props;
        const {data} = this.state;

        if (this.form.isFormValid()) {
            this.setState({isLoading: true});
            axios.post('/api/model/' + modelId + '/v/' + versionId + '/module/' + module.id + '/triple' + (data.id ? '/' + data.id : ''), data)
                .then((response) => {
                    this.setState({
                        isLoading: false,
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
                    this.setState({isLoading: false});
                });
        }
    };

    getOptions = (type) => {
        const {nodes} = this.props;

        return nodes[type].map((node) => {
            return {value: node.id, label: node.title, repeated: node.repeated};
        });
    };

    render() {
        const {show, handleClose, module} = this.props;
        const {data, validation, isLoading} = this.state;

        const required = "This field is required";
        const validUrl = "Please enter a valid URI";

        const subjectSelectable = (data.subjectType === 'internal' || data.subjectType === 'external');
        let subjectOptions = subjectSelectable ? this.getOptions(data.subjectType) : [];
        const objectSelectable = (data.objectType === 'internal' || data.objectType === 'external' || data.objectType === 'value');
        let objectOptions = objectSelectable ? this.getOptions(data.objectType) : [];

        if (module && data.objectType === 'value' && module.repeated) {
            objectOptions = objectOptions.filter((option) => {
                return option.repeated;
            })
        }

        if (module && data.objectType === 'internal' && !module.repeated) {
            objectOptions = objectOptions.filter((option) => {
                return option.repeated === false;
            })
        }

        if (module && data.subjectType === 'internal' && !module.repeated) {
            subjectOptions = subjectOptions.filter((option) => {
                return option.repeated === false;
            })
        }

        return <Modal
            show={show}
            className="TripleModal"
            handleClose={handleClose}
            title={data.id ? 'Edit triple' : 'Add triple'}
            closeButton
            footer={(
                <Button type="submit" disabled={isLoading} onClick={() => this.form.submit()}>
                    {data.id ? 'Edit triple' : 'Add triple'}
                </Button>
            )}
        >
            <ValidatorForm
                ref={node => (this.form = node)}
                onSubmit={this.handleSubmit}
                method="post"
            >

                <FormHeading label="Subject"/>
                <Stack>
                    <FormItem label="Type">
                        <Dropdown
                            validators={['required']}
                            errorMessages={[required]}
                            options={tripleTypes.subject}
                            onChange={this.handleSubjectTypeChange}
                            value={tripleTypes.subject.find((option) => {
                                return data.subjectType === option.value
                            }) || null}
                            serverError={validation.subjectType}
                            name="subjectType"
                            width="tiny"
                            menuPosition="fixed"
                        />
                    </FormItem>

                    {subjectSelectable && <FormItem label="Node">
                        <Dropdown
                            validators={['required']}
                            errorMessages={[required]}
                            options={subjectOptions}
                            onChange={this.handleSubjectValueChange}
                            value={subjectOptions.find((option) => {
                                return data.subjectValue === option.value
                            }) || null}
                            serverError={validation.subjectValue}
                            name="subjectValue"
                            width="small"
                            menuPosition="fixed"
                        />
                    </FormItem>}
                </Stack>

                <FormHeading label="Predicate"/>

                <FormItem label="URI">
                    <Input
                        validators={['required', 'isUrl']}
                        errorMessages={[required, validUrl]}
                        name="predicateValue"
                        onChange={this.handlePredicateChange}
                        value={data.predicateValue}
                        serverError={validation.predicateValue}
                        width="100%"
                        inputSize="100%"
                    />
                </FormItem>

                <FormHeading label="Object"/>

                <Stack>
                    <FormItem label="Type">
                        <Dropdown
                            validators={['required']}
                            errorMessages={[required]}
                            options={tripleTypes.object}
                            onChange={this.handleObjectTypeChange}
                            value={tripleTypes.object.find((option) => {
                                return data.objectType === option.value
                            }) || null}
                            serverError={validation.objectType}
                            name="objectType"
                            width="tiny"
                            menuPosition="fixed"
                        />
                    </FormItem>

                    {objectSelectable && <FormItem label="Node">
                        <Dropdown
                            validators={['required']}
                            errorMessages={[required]}
                            options={objectOptions}
                            onChange={this.handleObjectValueChange}
                            value={objectOptions.find((option) => {
                                return data.objectValue === option.value
                            }) || null}
                            serverError={validation.objectValue}
                            name="objectValue"
                            width="small"
                            menuPosition="fixed"
                        />
                    </FormItem>}
                </Stack>
            </ValidatorForm>
        </Modal>
    }
}

export const tripleTypes = {
    subject: [
        {value: 'internal', label: 'Internal'},
        {value: 'external', label: 'External'},
        {value: 'record', label: 'Record'},
    ],
    object: [
        {value: 'internal', label: 'Internal'},
        {value: 'external', label: 'External'},
        {value: 'record', label: 'Record'},
        {value: 'literal', label: 'Literal'},
        {value: 'value', label: 'Value'}
    ]
};

const defaultData = {
    subjectType: 'internal',
    subjectValue: '',
    predicateValue: '',
    objectType: 'internal',
    objectValue: '',
};
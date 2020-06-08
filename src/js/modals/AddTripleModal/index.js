import React, {Component} from 'react'
import Modal from "react-bootstrap/Modal";
import {ValidatorForm} from "react-form-validator-core";
import FormItem from "../../components/Form/FormItem";
import Container from "react-bootstrap/Container";
import FormHeading from "../../components/Form/FormHeading";
import Input from "../../components/Input";
import Col from "react-bootstrap/Col";
import Row from "react-bootstrap/Row";
import Dropdown from "../../components/Input/Dropdown";
import axios from "axios";
import {toast} from "react-toastify";
import ToastContent from "../../components/ToastContent";
import {Button} from "@castoredc/matter";

export default class AddTripleModal extends Component {
    constructor(props) {
        super(props);

        this.state = {
            data: defaultData,
            validation: {},
            showFieldSelector: false,
            isLoading: false
        };
    }

    componentDidUpdate(prevProps, prevState, snapshot) {
        if (this.props.show !== prevProps.show) {
            this.setState({
                data: defaultData,
            })
        }
    }

    componentDidMount() {
        ValidatorForm.addValidationRule('isUrl', (value) => {
            var pattern = new RegExp('^((ft|htt)ps?:\\/\\/)?'+ // protocol
                '((([a-z\\d]([a-z\\d-]*[a-z\\d])*)\\.)+[a-z]{2,}|'+ // domain name and extension
                '((\\d{1,3}\\.){3}\\d{1,3}))'+ // OR ip (v4) address
                '(\\:\\d+)?'+ // port
                '(\\/[-a-z\\d%@_.~+&:]*)*'+ // path
                '(\\?[;&a-z\\d%@_.,~+&:=-]*)?'+ // query string
                '(\\#[-a-z\\d_]*)?$','i'); // fragment locator
            return pattern.test(value);
        });
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

    handleSubjectTypeChange = (event) => {
        const { data } = this.state;

        this.setState({
            data: {
                ...data,
                subjectType: event.value,
                subjectValue: ''
            }
        }, () => {
            console.log(this.state);
        });
    };

    handleSubjectValueChange = (event) => {
        const { data } = this.state;

        this.setState({
            data: {
                ...data,
                subjectValue: event.value
            }
        });
    };

    handleObjectTypeChange = (event) => {
        const { data } = this.state;

        this.setState({
            data: {
                ...data,
                objectType: event.value,
                objectValue: ''
            }
        });
    };

    handleObjectValueChange = (event) => {
        const { data } = this.state;

        this.setState({
            data: {
                ...data,
                objectValue: event.value
            }
        });
    };

    handleSubmit = (event) => {
        const {modelId, moduleId, onSaved} = this.props;
        const {data} = this.state;
        event.preventDefault();

        if (this.form.isFormValid()) {
            this.setState({isLoading: true});
            axios.post('/api/model/' + modelId + '/module/' + moduleId + '/triple/add', data)
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
            return { value: node.id, label: node.title };
        });
    };

    render() {
        const { show, handleClose } = this.props;
        const { data, validation, isLoading } = this.state;

        const required = "This field is required";
        const validUrl = "Please enter a valid URI";

        const subjectSelectable = (data.subjectType === 'internal' || data.subjectType === 'external');
        const subjectOptions = subjectSelectable ? this.getOptions(data.subjectType) : [];
        const objectSelectable = (data.objectType === 'internal' || data.objectType === 'external' || data.objectType === 'value');
        const objectOptions = objectSelectable ? this.getOptions(data.objectType) : [];

        return <Modal show={show} onHide={handleClose} className="AddTripleModal">
            <ValidatorForm
                ref={node => (this.form = node)}
                onSubmit={this.handleSubmit}
                method="post"
            >
                <Modal.Header closeButton>
                    <Modal.Title>Add triple</Modal.Title>
                </Modal.Header>
                <Modal.Body>
                    <Container>
                        <Row>
                            <Col md={12}>
                                <FormHeading label="Subject" />
                            </Col>
                            <Col md={6}>
                                <FormItem label="Type">
                                    <Dropdown
                                        validators={['required']}
                                        errorMessages={[required]}
                                        options={tripleTypes.subject}
                                        onChange={this.handleSubjectTypeChange}
                                        value={tripleTypes.subject.find((option) => {return data.subjectType === option.value}) || null}
                                        serverError={validation.subjectType}
                                        name="subjectType"
                                        width="fullWidth"
                                    />
                                </FormItem>
                            </Col>
                            <Col md={6}>
                                {subjectSelectable && <FormItem label="Node">
                                    <Dropdown
                                        validators={['required']}
                                        errorMessages={[required]}
                                        options={subjectOptions}
                                        onChange={this.handleSubjectValueChange}
                                        value={subjectOptions.find((option) => {return data.subjectValue === option.value}) || null}
                                        serverError={validation.subjectValue}
                                        name="subjectValue"
                                        width="fullWidth"
                                    />
                                </FormItem>}
                            </Col>
                        </Row>
                        <Row>
                            <Col md={12}>
                                <FormHeading label="Predicate" />
                            </Col>
                            <Col md={6}>
                                <FormItem label="URI">
                                    <Input
                                        validators={['required', 'isUrl']}
                                        errorMessages={[required, validUrl]}
                                        name="predicateValue"
                                        onChange={this.handleChange}
                                        value={data.predicateValue}
                                        serverError={validation.predicateValue}
                                    />
                                </FormItem>
                            </Col>
                        </Row>
                        <Row>
                            <Col md={12}>
                                <FormHeading label="Object" />
                            </Col>
                            <Col md={6}>
                                <FormItem label="Type">
                                    <Dropdown
                                        validators={['required']}
                                        errorMessages={[required]}
                                        options={tripleTypes.object}
                                        onChange={this.handleObjectTypeChange}
                                        value={tripleTypes.object.find((option) => {return data.objectType === option.value}) || null}
                                        serverError={validation.objectType}
                                        name="objectType"
                                        width="fullWidth"
                                    />
                                </FormItem>
                            </Col>
                            <Col md={6}>
                                {objectSelectable && <FormItem label="Node">
                                    <Dropdown
                                        validators={['required']}
                                        errorMessages={[required]}
                                        options={objectOptions}
                                        onChange={this.handleObjectValueChange}
                                        value={objectOptions.find((option) => {return data.objectValue === option.value}) || null}
                                        serverError={validation.objectValue}
                                        name="objectValue"
                                        width="fullWidth"
                                    />
                                </FormItem>}
                            </Col>
                        </Row>
                    </Container>
                </Modal.Body>
                <Modal.Footer>
                    <Button type="submit" disabled={isLoading}>
                        Add triple
                    </Button>
                </Modal.Footer>
            </ValidatorForm>
        </Modal>
    }
}

export const tripleTypes = {
    subject: [
        { value: 'internal', label: 'Internal' },
        { value: 'external', label: 'External' },
        { value: 'record', label: 'Record' },
    ],
    object: [
        { value: 'internal', label: 'Internal' },
        { value: 'external', label: 'External' },
        { value: 'record', label: 'Record' },
        { value: 'literal', label: 'Literal' },
        { value: 'value', label: 'Value' }
    ]
};

const defaultData = {
    subjectType: 'internal',
    subjectValue: '',
    predicateValue: '',
    objectType: 'internal',
    objectValue: '',
};
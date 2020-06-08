import React, {Component} from 'react'
import Modal from "react-bootstrap/Modal";
import {ValidatorForm} from "react-form-validator-core";
import axios from "axios";
import {toast} from "react-toastify";
import ToastContent from "../../components/ToastContent";
import Dropdown from "../../components/Input/Dropdown";
import FormItem from "../../components/Form/FormItem";
import Spinner from "react-bootstrap/Spinner";
import {Button} from "@castoredc/matter";

export default class AddAnnotationModal extends Component {
    constructor(props) {
        super(props);

        this.state = {
            data:        defaultData,
            validation:  {},
            ontologies:  [],
            axiosCancel: null,
            isLoading:   false,
        };
    }

    componentDidMount() {
        this.getOntologies();
    }

    componentDidUpdate(prevProps, prevState, snapshot) {
        if (this.props.entity !== prevProps.entity) {
            this.setState({
                data: defaultData,
            })
        }
    }

    getOntologies = () => {
        axios.get('/api/terminology/ontologies')
            .then((response) => {
                this.setState({
                    ontologies: response.data,
                });
            })
            .catch((error) => {
                if (error.response && typeof error.response.data.error !== "undefined") {
                    toast.error(<ToastContent type="error" message={error.response.data.error}/>);
                } else {
                    toast.error(<ToastContent type="error" message="An error occurred"/>);
                }
            });
    };

    loadConcepts = (inputValue, callback) => {
        const {data, axiosCancel} = this.state;

        if (data.ontology === null) {
            return null;
        }

        if (axiosCancel !== null) {
            axiosCancel.cancel();
        }

        const CancelToken = axios.CancelToken;
        const source = CancelToken.source();

        this.setState({
            axiosCancel: source,
        });

        axios.get('/api/terminology/concepts', {
            cancelToken: source.token,
            params:      {
                ontology: data.ontology.value,
                query:    inputValue,
            },
        }).then((response) => {
            callback(response.data);
        })
            .catch((error) => {
                if (!axios.isCancel(error)) {
                    if (error.response && typeof error.response.data.error !== "undefined") {
                        toast.error(<ToastContent type="error" message={error.response.data.error}/>);
                    } else {
                        toast.error(<ToastContent type="error" message="An error occurred"/>);
                    }
                }
                callback(null);
            });
    };

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

    handleOntologyChange = (event) => {
        const {data} = this.state;

        this.setState({
            data: {
                ...data,
                ontology: event,
                concept:  null,
            },
        });
    };

    handleConceptChange = (event) => {
        const {data} = this.state;

        this.setState({
            data: {
                ...data,
                concept: event,
            },
        });
    };

    handleSubmit = (event) => {
        const {entity, onSaved, studyId} = this.props;
        const {data} = this.state;
        event.preventDefault();

        if (this.form.isFormValid()) {
            this.setState({isLoading: true});

            axios.post('/api/study/' + studyId + '/annotations/add', {
                entityType:   entity.type,
                entityId:     entity.id,
                entityParent: entity.parent,
                ontology:     data.ontology.value,
                concept:      data.concept.value,
            })
                .then(() => {
                    this.setState({isLoading: false});
                    onSaved();
                })
                .catch((error) => {
                    if (error.response && error.response.status === 400) {
                        this.setState({
                            validation: error.response.data.fields,
                        });
                    } else if (error.response) {
                        toast.error(<ToastContent type="error" message={error.response.data.error}/>, {
                            position: "top-center",
                        });
                    } else {
                        toast.error(<ToastContent type="error" message="An error occurred"/>, {
                            position: "top-center",
                        });
                    }
                    this.setState({isLoading: false});
                });
        }
    };

    render() {
        const {show, handleClose} = this.props;
        const {data, ontologies, isLoading} = this.state;

        const required = "This field is required";
        const validUrl = "Please enter a valid URI";

        const options = ontologies.map((ontology) => {
            return {value: ontology.id, label: ontology.name};
        });

        return <Modal show={show} onHide={handleClose} className="AddAnnotationModal">
            <ValidatorForm
                ref={node => (this.form = node)}
                onSubmit={this.handleSubmit}
                method="post"
            >
                <Modal.Header closeButton>
                    <Modal.Title>Add annotation</Modal.Title>
                </Modal.Header>
                <Modal.Body>
                    <FormItem label="Ontology">
                        <Dropdown
                            validators={['required']}
                            errorMessages={[required]}
                            options={options}
                            name="ontology"
                            value={data.ontology}
                            onChange={this.handleOntologyChange}
                        />
                    </FormItem>
                    <FormItem label="Concept">
                        <Dropdown
                            validators={['required']}
                            errorMessages={[required]}
                            name="concept"
                            value={data.concept}
                            async
                            loadOptions={this.loadConcepts}
                            onChange={this.handleConceptChange}
                            isDisabled={data.ontology === null}
                        />
                    </FormItem>
                </Modal.Body>
                <Modal.Footer>
                    <Button type="submit" disabled={isLoading}>
                        Add annotation
                    </Button>
                </Modal.Footer>
            </ValidatorForm>
        </Modal>
    }
}

const defaultData = {
    ontology: null,
    concept:  null,
};
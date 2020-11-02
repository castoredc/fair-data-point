import React, {Component} from 'react'
import axios from "axios";
import {toast} from "react-toastify";
import ToastContent from "../../ToastContent";
import Dropdown from "../../Input/Dropdown";
import FormItem from "../FormItem";
import {Button} from "@castoredc/matter";
import CheckboxGroup from "../../Input/CheckboxGroup";
import './OntologyConceptFormBlock.scss';

export default class OntologyConceptFormBlock extends Component {
    constructor(props) {
        super(props);

        this.state = {
            data: defaultData,
            validation: {},
            ontologies: [],
            axiosCancel: null,
            isLoading: false,
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
            params: {
                ontology: data.ontology.value,
                query: inputValue,
                includeIndividuals: data.includeIndividuals
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

    handleOntologyChange = (event) => {
        const {data} = this.state;

        this.setState({
            data: {
                ...data,
                ontology: event,
            },
        });
    };

    handleConceptChange = (concept) => {
        const {value, handleChange, name} = this.props;
        const {data} = this.state;

        const newConcept = {
            code: concept.code,
            url: concept.url,
            displayName: concept.label,
            ontology: data.ontology
        };

        let newConcepts = value;
        newConcepts.push(newConcept);

        handleChange({target: {name: name, value: newConcepts}});
    };

    removeConcept = (index) => {
        const {value, handleChange} = this.props;

        let newConcepts = value;
        newConcepts.splice(index, 1);

        handleChange({target: {name: name, value: newConcepts}});
    };

    render() {
        const {label, value} = this.props;
        const {data, ontologies} = this.state;

        const options = ontologies.map((ontology) => {
            return {...ontology, value: ontology.id, label: ontology.name};
        });

        return <FormItem label={label}>
            <div className="OntologyConceptFormBlock">
                <div className="Header Row">
                    <div className="Ontology">
                        Ontology
                    </div>
                    <div className="Concept">
                        Concept
                    </div>
                </div>

                <div className="Concepts">
                    {value.map((concept, index) => {
                        return <div className="Row" key={index}>
                            <div className="Ontology">
                                {concept.ontology.name}
                            </div>
                            <div className="ConceptCode">
                                {concept.code}
                            </div>
                            <div className="Concept">
                                {concept.displayName}
                            </div>
                            <div className="Buttons">
                                <Button buttonType="contentOnly" icon="cross" className="RemoveButton"
                                        onClick={() => this.removeConcept(index)} iconDescription="Remove"/>
                            </div>
                        </div>
                    })}
                </div>

                <div className="AddNew Row">
                    <div className="Ontology">
                        <Dropdown
                            options={options}
                            name="ontology"
                            value={data.ontology}
                            onChange={this.handleOntologyChange}
                            menuPosition="fixed"
                            width="tiny"
                        />
                    </div>
                    <div className="Concept">
                        <Dropdown
                            name="concept"
                            value={data.concept}
                            async
                            loadOptions={this.loadConcepts}
                            onChange={this.handleConceptChange}
                            isDisabled={data.ontology === null}
                            menuPosition="fixed"
                        />

                        <CheckboxGroup
                            options={[{value: '1', label: 'Include individuals'}]}
                            value={data.includeIndividuals}
                            name="includeIndividuals"
                            onChange={this.handleChange}
                        />
                    </div>
                </div>
            </div>
        </FormItem>
    }
}

const defaultData = {
    ontology: null,
    concept: null,
    includeIndividuals: []
};
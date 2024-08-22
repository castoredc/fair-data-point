import React, { Component } from 'react';
import { toast } from 'react-toastify';
import ToastItem from 'components/ToastItem';
import debounce from 'lodash/debounce';
import { Button, Choice, DefaultOptionType, Dropdown, Icon, ReactSelectTypes } from '@castoredc/matter';
import FormItem from 'components/Form/FormItem';
import { FieldProps } from 'formik';
import { apiClient } from 'src/js/network';
import { AsyncDropdownIndicator, isMultipleOption } from 'components/Input/Formik/Select';
import FieldErrors from 'components/Input/Formik/Errors';

interface OntologyConceptFormBlockProps extends FieldProps {
    serverError?: any;
}

type IsMulti = boolean;

type OntologyConceptFormBlockState = {
    ontologies: any;
    includeIndividuals: boolean;
    selectedOntology: DefaultOptionType | null;
};

export default class OntologyConceptFormBlock extends Component<OntologyConceptFormBlockProps, OntologyConceptFormBlockState> {
    constructor(props) {
        super(props);

        this.state = {
            ontologies: [],
            includeIndividuals: false,
            selectedOntology: null,
        };
    }

    componentDidMount() {
        this.getOntologies();
    }

    getOntologies = () => {
        apiClient
            .get('/api/terminology/ontologies')
            .then(response => {
                this.setState({
                    ontologies: response.data,
                });
            })
            .catch(error => {
                if (error.response && typeof error.response.data.error !== 'undefined') {
                    toast.error(<ToastItem type="error" title={error.response.data.error} />);
                } else {
                    toast.error(<ToastItem type="error" title="An error occurred" />);
                }
            });
    };

    loadConcepts = debounce((inputValue, callback) => {
        const { selectedOntology, includeIndividuals } = this.state;

        if (selectedOntology === null) {
            return;
        }

        apiClient
            .get('/api/terminology/concepts', {
                params: {
                    ontology: selectedOntology.value,
                    query: inputValue,
                    includeIndividuals: includeIndividuals,
                },
            })
            .then(response => {
                callback(response.data);
            })
            .catch(error => {
                if (error.response && typeof error.response.data.error !== 'undefined') {
                    toast.error(<ToastItem type="error" title={error.response.data.error} />);
                } else {
                    toast.error(<ToastItem type="error" title="An error occurred" />);
                }
                callback(null);
            });
    }, 300);

    setIncludeIndividuals = () => {
        const { includeIndividuals } = this.state;

        this.setState({
            includeIndividuals: !includeIndividuals,
        });
    };

    handleOntologyChange = ontology => {
        this.setState({
            selectedOntology: ontology,
        });
    };

    addConcept = (field, form, concept) => {
        const { selectedOntology } = this.state;

        const newConcept = {
            code: concept.code,
            url: concept.url,
            displayName: concept.label,
            ontology: selectedOntology,
        };

        let newConcepts = field.value;
        newConcepts.push(newConcept);

        form.setFieldValue(field.name, newConcepts);
    };

    removeConcept = (field, form, index) => {
        let newConcepts = field.value;
        newConcepts.splice(index, 1);

        form.setFieldValue(field.name, newConcepts);
    };

    render() {
        const { field, form, serverError } = this.props;
        const { includeIndividuals, selectedOntology, ontologies } = this.state;

        const touched = form.touched[field.name];
        const errors = form.errors[field.name];
        const serverErrors = serverError ? serverError[field.name] : undefined;

        const value = field.value ? field.value : [defaultData];

        const options = ontologies.map(ontology => {
            return { ...ontology, value: ontology.id, label: ontology.name };
        });

        return (
            <>
            <div className="OntologyConceptFormBlock">
                <div className="Header Row">
                    <div className="Ontology">Ontology</div>
                    <div className="Concept">Concept</div>
                </div>

                <div className="Concepts">
                    {value.map((concept, index) => {
                        const ontology = typeof concept.ontology === 'string' ? ontologies.find((ontology) => ontology.id === concept.ontology) : concept.ontology;

                        return (
                            <div className="Row" key={index}>
                                <div className="Ontology">{ontology.name}</div>
                                <div className="ConceptCode">{concept.code}</div>
                                <div className="Concept">{concept.displayName}</div>
                                <div className="Buttons">
                                    <Button
                                        buttonType="contentOnly"
                                        icon="cross"
                                        className="RemoveButton"
                                        onClick={() => this.removeConcept(field, form, index)}
                                        iconDescription="Remove"
                                    />
                                </div>
                            </div>
                        );
                    })}
                </div>

                <div className="AddNew Row">
                    <div className="Ontology">
                        <Dropdown
                            options={options}
                            value={selectedOntology}
                            onChange={(value: ReactSelectTypes.OnChangeValue<DefaultOptionType, IsMulti>, action: ReactSelectTypes.ActionMeta<DefaultOptionType>) => {
                                const returnValue = value && (isMultipleOption(value) ? value[0] : value);
                                this.handleOntologyChange(returnValue);
                            }}
                            width="tiny"
                            menuPlacement={'auto'}
                            menuPosition="fixed"
                        />
                    </div>
                    <div className="Concept">
                        <Dropdown
                            openMenuOnClick={false}
                            value={null}
                            loadOptions={this.loadConcepts}
                            options={[]}
                            onChange={(value: ReactSelectTypes.OnChangeValue<DefaultOptionType, IsMulti>, action: ReactSelectTypes.ActionMeta<DefaultOptionType>) => {
                                const returnValue = value && (isMultipleOption(value) ? value[0] : value);
                                this.addConcept(field, form, returnValue);
                            }}
                            isDisabled={selectedOntology === null}
                            menuPosition="fixed"
                            components={{ DropdownIndicator: AsyncDropdownIndicator }}
                            placeholder=""
                        />

                        <Choice
                            options={[{ value: '1', labelText: 'Include individuals' }]}
                            name="includeIndividuals"
                            onChange={this.setIncludeIndividuals}
                            hideLabel={true}
                            labelText=""
                            multiple={true}
                        />
                    </div>
                </div>
            </div>

            <FieldErrors field={field} serverErrors={serverErrors} />
            </>
        );
    }
}

const defaultData = {
    ontology: null,
    concept: null,
    includeIndividuals: [],
};

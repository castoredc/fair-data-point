import React, {Component} from 'react'
import axios, {CancelTokenSource} from "axios";
import {toast} from "react-toastify";
import ToastContent from "components/ToastContent";
import FormItem from "components/Form/FormItem";
import {Button, Modal} from "@castoredc/matter";
import {Field, Form, Formik} from "formik";
import Select, {AsyncSelect} from "components/Input/Formik/Select";
import * as Yup from "yup";
import Choice from "components/Input/Formik/Choice";

type AddAnnotationModalProps = {
    open: boolean,
    onClose: () => void,
    entity: any,
    onSaved: () => void,
    studyId: string,
}

type AddAnnotationModalState = {
    ontologies: any,
    axiosCancel: CancelTokenSource | null,
    validation: any,
}

export default class AddAnnotationModal extends Component<AddAnnotationModalProps, AddAnnotationModalState> {
    constructor(props) {
        super(props);

        this.state = {
            ontologies: [],
            axiosCancel: null,
            validation: {},
        };
    }

    componentDidMount() {
        this.getOntologies();
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

    loadConcepts = (ontology, includeIndividuals, inputValue, callback) => {
        const {axiosCancel} = this.state;

        if (ontology === null) {
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
                ontology: ontology,
                query: inputValue,
                includeIndividuals: includeIndividuals
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

    handleSubmit = (values, {setSubmitting}) => {
        const {entity, onSaved, studyId} = this.props;

        axios.post('/api/study/' + studyId + '/annotations/add', {
            entityType: entity.type,
            entityId: entity.id,
            entityParent: entity.parent,
            ontology: values.ontology,
            concept: values.concept.value,
            conceptType: values.concept.type
        })
            .then(() => {
                setSubmitting(false);
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

                setSubmitting(false);
            });
    };

    render() {
        const {open, onClose, entity} = this.props;
        const {ontologies, validation} = this.state;

        const options = ontologies.map((ontology) => {
            return {value: ontology.id, label: ontology.name};
        });

        if (!entity) {
            return null;
        }

        return <Modal
            accessibleName="Test"
            open={open}
            title={`Add annotation for ${entity.title}`}
            onClose={onClose}
        >
            <Formik
                initialValues={{
                    ontology: null,
                    concept:  null,
                    includeIndividuals: []
                }}
                validationSchema={Yup.object().shape({
                    ontology: Yup.string().required('Please select an ontology'),
                    concept: Yup.object().shape({
                        value: Yup.string(),
                        type: Yup.string().required(),
                    }).required('Please select a concept')
                })}
                onSubmit={this.handleSubmit}
            >
                {({
                      values,
                      errors,
                      touched,
                      handleChange,
                      handleBlur,
                      handleSubmit,
                      isSubmitting,
                      setValues,
                    setFieldValue
                  }) => {
                    return <Form>
                    <FormItem label="Ontology">
                        <Field
                            component={Select}
                            options={options}
                            name="ontology"
                            onChange={() => setFieldValue('concept', null)}
                            menuPosition="fixed"
                            serverError={validation}
                        />
                    </FormItem>
                    <FormItem label="Concept">
                        <Field
                            component={AsyncSelect}
                            name="concept"
                            async
                            loadOptions={(inputValue, callback) => this.loadConcepts(values.ontology, values.includeIndividuals, inputValue, callback)}
                            // onChange={this.handleConceptChange}
                            isDisabled={values.ontology === null}
                            menuPosition="fixed"
                            serverError={validation}
                        />

                        <Field
                            component={Choice}
                            multiple={true}
                            options={[{value: '1', labelText: 'Include individuals'}]}
                            name="includeIndividuals"
                            serverError={validation}
                        />
                    </FormItem>

                    <Button type="submit" disabled={values.ontology === null || isSubmitting}>
                        Add annotation
                    </Button>
                </Form>;
                }}
            </Formik>
        </Modal>
    }
}
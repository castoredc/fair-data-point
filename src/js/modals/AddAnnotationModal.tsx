import React, { Component } from 'react';
import debounce from 'lodash/debounce';
import Button from '@mui/material/Button';
import Modal from 'components/Modal';
import FormItem from 'components/Form/FormItem';
import { Field, Form, Formik } from 'formik';
import Select, { AsyncSelect } from 'components/Input/Formik/Select';
import * as Yup from 'yup';
import Choice from 'components/Input/Formik/Choice';
import { apiClient } from '../network';
import withNotifications, { ComponentWithNotifications } from 'components/WithNotifications';

interface AddAnnotationModalProps extends ComponentWithNotifications {
    open: boolean;
    onClose: () => void;
    entity: any;
    onSaved: () => void;
    studyId: string;
}

type AddAnnotationModalState = {
    ontologies: any;
    validation: any;
};

class AddAnnotationModal extends Component<AddAnnotationModalProps, AddAnnotationModalState> {
    constructor(props) {
        super(props);

        this.state = {
            ontologies: [],
            validation: {},
        };
    }

    componentDidMount() {
        this.getOntologies();
    }

    getOntologies = () => {
        const { notifications } = this.props;

        apiClient
            .get('/api/terminology/ontologies')
            .then(response => {
                this.setState({
                    ontologies: response.data,
                });
            })
            .catch(error => {
                if (error.response && typeof error.response.data.error !== 'undefined') {
                    notifications.show(error.response.data.error, { variant: 'error' });
                } else {
                    notifications.show('An error occurred', { variant: 'error' });
                }
            });
    };

    loadConcepts = debounce((ontology, includeIndividuals, inputValue, callback) => {
        if (ontology === null) {
            return null;
        }

        const { notifications } = this.props;

        apiClient
            .get('/api/terminology/concepts', {
                params: {
                    ontology: ontology,
                    query: inputValue,
                    includeIndividuals: includeIndividuals,
                },
            })
            .then(response => {
                callback(response.data);
            })
            .catch(error => {
                if (error.response && typeof error.response.data.error !== 'undefined') {
                    notifications.show(error.response.data.error, { variant: 'error' });
                } else {
                    notifications.show('An error occurred', { variant: 'error' });
                }

                callback(null);
            });
    }, 300);

    handleSubmit = (values, { setSubmitting }) => {
        const { entity, onSaved, studyId, notifications } = this.props;

        apiClient
            .post('/api/study/' + studyId + '/annotations/add', {
                entityType: entity.type,
                entityId: entity.id,
                entityParent: entity.parent,
                ontology: values.ontology,
                concept: values.concept.value,
                conceptType: values.concept.type,
            })
            .then(() => {
                setSubmitting(false);
                onSaved();
            })
            .catch(error => {
                if (error.response && error.response.status === 400) {
                    this.setState({
                        validation: error.response.data.fields,
                    });
                } else if (error.response) {
                    notifications.show(error.response.data.error, { variant: 'error' });
                } else {
                    notifications.show('An error occurred', { variant: 'error' });
                }

                setSubmitting(false);
            });
    };

    render() {
        const { open, onClose, entity } = this.props;
        const { ontologies, validation } = this.state;

        const options = ontologies.map(ontology => {
            return { value: ontology.id, label: ontology.name };
        });

        if (!entity) {
            return null;
        }

        return (
            <Modal open={open} title={`Add annotation for ${entity.title}`} onClose={onClose}>
                <Formik
                    initialValues={{
                        ontology: null,
                        concept: null,
                        includeIndividuals: [],
                    }}
                    validationSchema={Yup.object().shape({
                        ontology: Yup.string().required('Please select an ontology'),
                        concept: Yup.object()
                            .shape({
                                value: Yup.string(),
                                type: Yup.string().required(),
                            })
                            .required('Please select a concept'),
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
                          setFieldValue,
                      }) => {
                        return (
                            <Form>
                                <FormItem label="Ontology">
                                    <Field
                                        component={Select}
                                        options={options}
                                        name="ontology"
                                        onChange={() => setFieldValue('concept', null)}

                                        serverError={validation}
                                    />
                                </FormItem>
                                <FormItem label="Concept">
                                    <Field
                                        component={AsyncSelect}
                                        name="concept"
                                        async
                                        loadOptions={(inputValue, callback) =>
                                            this.loadConcepts(values.ontology, values.includeIndividuals, inputValue, callback)
                                        }
                                        // onChange={this.handleConceptChange}
                                        isDisabled={values.ontology === null}

                                        serverError={validation}
                                    />

                                    <Field
                                        component={Choice}
                                        multiple={true}
                                        options={[{ value: '1', label: 'Include individuals' }]}
                                        name="includeIndividuals"
                                        serverError={validation}
                                    />
                                </FormItem>

                                <Button
                                    type="submit"
                                    disabled={values.ontology === null || isSubmitting}
                                    variant="contained"
                                >
                                    Add annotation
                                </Button>
                            </Form>
                        );
                    }}
                </Formik>
            </Modal>
        );
    }
}

export default withNotifications(AddAnnotationModal);
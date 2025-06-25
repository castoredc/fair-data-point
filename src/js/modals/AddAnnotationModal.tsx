import React, { FC, useCallback, useEffect, useRef, useState } from 'react';
import { Form, Formik } from 'formik';
import * as Yup from 'yup';
import debounce from 'lodash/debounce';

// MUI Components
import { Autocomplete, Box, Button, Checkbox, FormControlLabel, TextField } from '@mui/material';

// Custom Components
import Modal from 'components/Modal';
import FormItem from 'components/Form/FormItem';

// Hooks and Utils
import { useNotifications } from 'components/WithNotifications';
import { apiClient } from 'src/js/network';

// Types
import { OntologyType } from 'types/OntologyType';

interface Entity {
    type: string;
    id: string;
    parent: string;
    title: string;
}

interface OntologyConceptType {
    code: string;
    label: string;
    type: string;
}

interface FormValues {
    ontology: OntologyType | null;
    concept: OntologyConceptType | null;
    includeIndividuals: boolean;
}

interface AddAnnotationModalProps {
    open: boolean;
    entity: Entity;
    onClose: () => void;
    studyId: string;
    onSaved: () => void;
}

const initialValues: FormValues = {
    ontology: null,
    concept: null,
    includeIndividuals: false,
};

const validationSchema = Yup.object({
    ontology: Yup.object().nullable().required('Please select an ontology'),
    concept: Yup.object().nullable().required('Please select a concept'),
});

const AddAnnotationModal: FC<AddAnnotationModalProps> = ({ open, onClose, entity, studyId, onSaved }) => {
    if (!entity) return null;

    // Notifications
    const notifications = useNotifications();
    const notificationsRef = useRef(notifications);

    // Ontology state
    const [ontologies, setOntologies] = useState<OntologyType[]>([]);
    const [selectedOntology, setSelectedOntology] = useState<OntologyType | null>(null);

    // Concept state
    const [conceptSearchValue, setConceptSearchValue] = useState<string>('');
    const [conceptOptions, setConceptOptions] = useState<OntologyConceptType[]>([]);
    const [loadingConcepts, setLoadingConcepts] = useState<boolean>(false);
    const [selectedConcept, setSelectedConcept] = useState<OntologyConceptType | null>(null);

    // Form state
    const [includeIndividuals, setIncludeIndividuals] = useState<boolean>(false);

    // Error handling
    useEffect(() => {
        notificationsRef.current = notifications;
    });

    const showError = useCallback((error: any) => {
        if (error?.response?.data?.error) {
            notificationsRef.current.show(error.response.data.error, { variant: 'error' });
        } else {
            notificationsRef.current.show('An error occurred', { variant: 'error' });
        }
    }, []);

    // Load ontologies on mount
    useEffect(() => {
        const getOntologies = async () => {
            try {
                const response = await apiClient.get('/api/terminology/ontologies');
                setOntologies(response.data);
            } catch (error: any) {
                showError(error);
            }
        };

        getOntologies();
    }, [showError]);

    // Concept search handling
    const fetchConceptsDebounced = React.useMemo(
        () =>
            debounce(async (searchValue: string) => {
                if (!selectedOntology || !searchValue) {
                    setConceptOptions([]);
                    return;
                }

                setLoadingConcepts(true);

                try {
                    const response = await apiClient.get('/api/terminology/concepts', {
                        params: {
                            ontology: selectedOntology.id,
                            query: searchValue,
                            includeIndividuals,
                        },
                    });
                    setConceptOptions(response.data ?? []);
                } catch (error: any) {
                    showError(error);
                    setConceptOptions([]);
                } finally {
                    setLoadingConcepts(false);
                }
            }, 300),
        [selectedOntology, includeIndividuals, showError],
    );

    // Cleanup debounced search on unmount
    useEffect(() => {
        return () => {
            fetchConceptsDebounced.cancel();
        };
    }, [fetchConceptsDebounced]);

    // Form handlers
    const handleSubmit = async (values: FormValues, { setSubmitting }: {
        setSubmitting: (isSubmitting: boolean) => void
    }) => {
        try {
            if (!values.ontology || !values.concept) {
                showError(new Error('Please select both ontology and concept'));
                return;
            }

            await apiClient.post(`/api/study/${studyId}/annotations/add`, {
                entityType: entity.type,
                entityId: entity.id,
                entityParent: entity.parent,
                ontology: values.ontology.id,
                concept: values.concept.code,
                conceptType: values.concept.type,
                includeIndividuals: values.includeIndividuals,
            });
            onSaved();
            onClose();
        } catch (error: any) {
            showError(error);
        } finally {
            setSubmitting(false);
        }
    };

    const handleOntologyChange = useCallback((ontology: OntologyType | null) => {
        setSelectedOntology(ontology);
        setConceptSearchValue('');
        setConceptOptions([]);
        setSelectedConcept(null);
    }, []);

    const handleConceptSearchInputChange = useCallback(
        (_event: React.SyntheticEvent<Element, Event>, newValue: string) => {
            setConceptSearchValue(newValue);
            fetchConceptsDebounced(newValue);
        },
        [fetchConceptsDebounced],
    );

    return (
        <Modal open={open} title={`Add annotation for ${entity.title}`} onClose={onClose}>
            <Formik
                initialValues={initialValues}
                onSubmit={handleSubmit}
                validationSchema={validationSchema}
                enableReinitialize
            >
                {({ isSubmitting, setFieldValue, values }) => (
                    <Form>
                        <Box sx={{ display: 'flex', flexDirection: 'column', gap: 2 }}>
                            {/* Ontology Selection */}
                            <FormItem label="Ontology" isRequired>
                                <Autocomplete
                                    options={ontologies}
                                    value={values.ontology}
                                    onChange={(_event: any, newValue: OntologyType | null) => {
                                        handleOntologyChange(newValue);
                                        setFieldValue('ontology', newValue);
                                        setFieldValue('concept', null);
                                    }}
                                    getOptionLabel={(option: OntologyType) => option.name}
                                    isOptionEqualToValue={(option, value) => option.id === value?.id}
                                    renderInput={(params) => (
                                        <TextField
                                            {...params}
                                            size="small"
                                            placeholder="Select ontology"
                                            required
                                        />
                                    )}
                                />
                            </FormItem>

                            {/* Concept Selection */}
                            <FormItem label="Concept" isRequired>
                                <Autocomplete
                                    disabled={!values.ontology}
                                    options={conceptOptions}
                                    value={values.concept}
                                    onInputChange={handleConceptSearchInputChange}
                                    onChange={(_event: any, newValue: OntologyConceptType | null) => {
                                        setSelectedConcept(newValue);
                                        setFieldValue('concept', newValue);
                                    }}
                                    getOptionLabel={(option: OntologyConceptType) => option.label}
                                    isOptionEqualToValue={(option, value) => option.code === value?.code}
                                    loading={loadingConcepts}
                                    renderInput={(params) => (
                                        <TextField
                                            {...params}
                                            size="small"
                                            placeholder="Search for a concept"
                                            required
                                        />
                                    )}
                                />
                            </FormItem>

                            {/* Include Individuals */}
                            <FormControlLabel
                                control={
                                    <Checkbox
                                        checked={values.includeIndividuals}
                                        onChange={(e) => {
                                            setIncludeIndividuals(e.target.checked);
                                            setFieldValue('includeIndividuals', e.target.checked);
                                        }}
                                        size="small"
                                    />
                                }
                                label="Include individuals"
                            />

                            {/* Actions */}
                            <Box sx={{ display: 'flex', justifyContent: 'flex-end', gap: 1, mt: 2 }}>
                                <Button
                                    type="submit"
                                    variant="contained"
                                    color="primary"
                                    disabled={isSubmitting || !values.ontology || !values.concept}
                                >
                                    Add annotation
                                </Button>
                            </Box>
                        </Box>
                    </Form>
                )}
            </Formik>
        </Modal>
    );
};

export default AddAnnotationModal;
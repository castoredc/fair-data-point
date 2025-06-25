import React, { FC, useCallback, useEffect, useState } from 'react';
import { FieldProps } from 'formik';
import { apiClient } from 'src/js/network';
import FieldErrors from 'components/Input/Formik/Errors';
import { ConceptList } from './ConceptList';
import { OntologyType } from 'types/OntologyType';
import { OntologyConceptSearchType } from 'types/OntologyConceptSearchType';
import { ComponentWithNotifications, useNotifications } from 'components/WithNotifications';

interface OntologyConceptFormBlockProps extends FieldProps, ComponentWithNotifications {
    serverError?: any;
}

const defaultData = {
    ontology: null,
    concept: null,
    includeIndividuals: [],
};

const OntologyConceptFormBlock: FC<OntologyConceptFormBlockProps> = ({
                                                                         field,
                                                                         form,
                                                                         serverError,
                                                                     }) => {
    const [ontologies, setOntologies] = useState<OntologyType[]>([]);
    const notifications = useNotifications();

    const formValue = field.value ? field.value : [defaultData];

    useEffect(() => {
        async function getOntologies() {
            try {
                const response = await apiClient.get('/api/terminology/ontologies');
                setOntologies(response.data);
            } catch (error: any) {
                if (error?.response?.data?.error) {
                    notifications.show(error.response.data.error, { variant: 'error' });
                } else {
                    notifications.show('An error occurred', { variant: 'error' });
                }
            }
        }

        getOntologies();
    }, []);

    const addConcept = useCallback(
        (concept: OntologyConceptSearchType | null, selectedOntology: OntologyType | null) => {
            if (!concept || !selectedOntology) return;

            const newConcept = {
                code: concept.code,
                url: concept.url,
                displayName: concept.label,
                ontology: selectedOntology,
            };

            const newConcepts = [...formValue, newConcept];
            form.setFieldValue(field.name, newConcepts);
        },
        [formValue, form, field.name],
    );

    const removeConcept = useCallback(
        (index: number) => {
            const newConcepts = [...formValue];
            newConcepts.splice(index, 1);
            form.setFieldValue(field.name, newConcepts);
        },
        [formValue, form, field.name],
    );

    const errors = form.errors[field.name];
    const serverErrors = serverError ? serverError[field.name] : undefined;

    const ontologyOptions = ontologies.map((ont) => ({
        ...ont,
        value: ont.id,
        label: ont.name,
    }));

    return (
        <>
            <div className="OntologyConceptFormBlock">
                <ConceptList
                    concepts={formValue}
                    ontologies={ontologies}
                    onRemoveConcept={removeConcept}
                    onAddConcept={addConcept}
                />
            </div>

            <FieldErrors field={field} serverErrors={serverErrors} />
        </>
    );
};

export default OntologyConceptFormBlock;
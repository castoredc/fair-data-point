import { OntologyType } from 'types/OntologyType';

export type OntologyConceptType = {
    code: string;
    url: string;
    displayName: string;
    ontology: OntologyType;
};

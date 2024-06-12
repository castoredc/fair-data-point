import { LocalizedTextType } from 'types/LocalizedTextType';
import { AgentListType } from 'types/AgentListType';
import { OntologyConceptType } from 'types/OntologyConceptType';

export type CatalogBrandType = {
    name: LocalizedTextType;
    accessingData: boolean;
};

export type CatalogMetadataType = {
    title: LocalizedTextType;
    version: {
        metadata: string;
    };
    description: LocalizedTextType;
    publishers: AgentListType;
    language: string | null;
    license: string | null;
    homepage: string | null;
    logo: string | null;
    themeTaxonomy: OntologyConceptType[];
    issued: string;
    modified: string;
};

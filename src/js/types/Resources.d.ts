import { IriType } from 'types/IriType';
import { Metadata } from 'types/Metadata';
import Distribution from 'pages/Main/Distribution';

export type PermissionsEnabledEntity = {
    permissions?: string[],
}

export interface Fdp extends PermissionsEnabledEntity {
    relativeUrl: string,
    iri: IriType,
    hasMetadata: boolean,
    defaultMetadataModel: string | null,
    metadata: Metadata,
    count: {
        catalog: number,
    },
};

export interface Catalog extends PermissionsEnabledEntity {
    relativeUrl: string,
    id: string,
    slug: string,
    defaultMetadataModel: string | null,
    acceptSubmissions: boolean,
    submissionAccessesData: boolean,
    hasMetadata: boolean,
    metadata: Metadata,
    count: {
        study: number,
        dataset: number,
    },
};

export interface Dataset extends PermissionsEnabledEntity {
    relativeUrl: string,
    id: string,
    slug: string,
    defaultMetadataModel: string | null,
    hasMetadata: boolean,
    metadata: Metadata,
    published: boolean,
    study: Study | null,
    count: {
        distribution: number,
    },
};

export interface Study extends PermissionsEnabledEntity {
    id: string,
    name: string,
    slug: string,
    hasMetadata: boolean,
    metadata: any,
    source: string,
    sourceId: string,
    sourceServer: string,
    published: boolean,
    count: {
        dataset: number,
    },
};


export interface GenericDistribution extends PermissionsEnabledEntity {
    relativeUrl: string,
    id: string,
    slug: string,
    defaultMetadataModel: string | null,
    hasMetadata: boolean,
    metadata: Metadata,
    hasContents: boolean,
    license: string | null,
    study: Study | null,
    hasApiUser: boolean,
};

export interface DistributionWithContents extends GenericDistribution {
    cached: boolean,
    public: boolean,
    type: 'rdf' | 'csv',
}

export interface RdfDistribution extends DistributionWithContents {
    fullUrl: string,
    accessUrl: string,
    downloadUrl: string,
    dataModel: DataModelVersion,
}
export interface CsvDistribution extends DistributionWithContents {
    downloadUrl: string,
    dataModel: DataDictionary,
}

export type Distribution = GenericDistribution | RdfDistribution | CsvDistribution;
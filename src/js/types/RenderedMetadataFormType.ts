export type RenderedMetadataFormType = {
    id: string;
    title: string;
    displayName: string;
    order: number;
    fields: RenderedMetadataFormFieldType[];
};

export type RenderedMetadataFormFieldType = {
    id: string;
    title: string;
    displayName: string;
    order: number;
    description?: string;
    fieldType: string;
    optionGroup?: string;
    value?: string;
    isRequired: boolean;
};

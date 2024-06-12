import { LocalizedTextItem } from 'types/LocalizedTextType';

export type Metadata = {
    id: string,
    version: string,
    model: string,
    modelVersion: string,
    createdAt: string,
    modifiedAt: string,
    title: LocalizedTextItem,
    description: LocalizedTextItem,
    contents: MetadataView,
};

export type MetadataView = {
    title: MetadataViewItem[],
    description: MetadataViewItem[],
    sidebar: MetadataViewItem[],
    modal: MetadataViewItem[],
}

export type MetadataViewItem = {
    title: string,
    order: number,
    type: string,
    dataType: string,
    value: any;
}

// export type MetadataViewValue = {
//
// }
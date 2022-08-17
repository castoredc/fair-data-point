import { IriType } from 'types/IriType';

export type NodeType = {
    id: string;
    type: string;
    title: string;
    description: string | null;
    value: IriType | NodeValueType | null;
    repeated: boolean;
};

export type NodeValueType = {
    dataType: string | null;
    value: string | null;
};

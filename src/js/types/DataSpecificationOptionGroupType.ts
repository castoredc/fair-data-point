import { NodeType } from 'types/NodeType';
import { IriType } from 'types/IriType';

export type DataSpecificationOptionGroupType = {
    id: string;
    title: string;
    description?: string;
    options: DataSpecificationOptionGroupOptionType[];
};

export type DataSpecificationOptionGroupOptionType = {
    id: string;
    title: string;
    description?: string;
    value: string;
    order: number;
}
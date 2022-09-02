import { NodeType } from 'types/NodeType';

export type NodesType = {
    external: NodeType[];
    internal: NodeType[];
    literal: NodeType[];
    record: NodeType[];
    value: NodeType[];
};

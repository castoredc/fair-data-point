import {NodeType} from "types/NodeType";
import {IriType} from "types/IriType";

export type ModuleType = {
    id: string,
    title: string,
    displayName: string,
    order: number,
    repeated: boolean,
    dependent: boolean,
    dependencies: DependenciesType[] | null,
    groupedTriples?: GroupedTripleType[],
    triples?: TripleType[],
};

export type DependenciesType = DependencyGroupType | DependencyRuleType;

export type DependencyDescription = {
    type: string,
    text: string,
};

export type DependencyGroupType = {
    id: string,
    group: string | null,
    combinator: string,
    rules: DependenciesType[],
    description: DependencyDescription[],
};

export type DependencyRuleType = {
    id: string,
    group: string | null,
    field: string,
    operator: string,
    value: string,
    description: DependencyDescription[],
};

export type TripleType = {
    id: string,
    subject: NodeType,
    predicate: PredicateType,
    object: NodeType,
};

export type PredicateType = {
    id: string,
    value: IriType,
    base: string,
};

export type GroupedTripleObjectType = NodeType & {
    tripleId: string,
};

export type GroupedTriplePredicateType = PredicateType & {
    objects: GroupedTripleObjectType[],
};

export type GroupedTripleType = NodeType & {
    predicates: GroupedTriplePredicateType[]
}

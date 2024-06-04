import { Predicate } from 'types/Predicate';

export interface TripleGroupProps {
    id: string;
    type: string;
    title: string;
    repeated: boolean;
    value: any;
    predicates: Predicate[];
    openTripleModal: (data: any) => void;
    openRemoveTripleModal: (data: any) => void;
}

export interface TriplePredicateProps {
    id: string;
    value: any;
    objects: TripleObjectProps[];
    data: any;
    openTripleModal: (data: any) => void;
    openRemoveTripleModal: (data: any) => void;
}

export interface TripleObjectProps {
    id: string;
    type: string;
    title: string;
    description: string;
    value: any;
    repeated: boolean;
    data: any;
    tripleId: string;
    openTripleModal: (data: any) => void;
    openRemoveTripleModal: (data: any) => void;
}
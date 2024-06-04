import { TripleObjectProps } from 'components/DataSpecification/types';

export type Predicate = {
    id: string;
    value: any;
    objects: TripleObjectProps[];
}
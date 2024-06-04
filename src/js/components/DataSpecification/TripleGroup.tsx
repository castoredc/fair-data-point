import React from 'react';
import TriplePredicate from './TriplePredicate';
import { Node } from './Node';
import { TripleGroupProps } from './types';

const TripleGroup: React.FC<TripleGroupProps> = (props) => {
    const { id, type, title, repeated, value, predicates, openTripleModal, openRemoveTripleModal } = props;

    const newData = {
        subjectType: type,
        subjectValue: id,
    };

    return (
        <div className="DataSpecificationTriple">
            <div className="DataSpecificationSubject">{Node(title, type, value, repeated)}</div>

            <div className="DataSpecificationPredicateObjects">
                {predicates.map(predicate => (
                    <TriplePredicate
                        key={predicate.id}
                        id={predicate.id}
                        value={predicate.value}
                        objects={predicate.objects}
                        data={newData}
                        openTripleModal={openTripleModal}
                        openRemoveTripleModal={openRemoveTripleModal}
                    />
                ))}
            </div>
        </div>
    );
};

export default TripleGroup;
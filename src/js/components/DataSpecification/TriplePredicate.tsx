import React from 'react';
import TripleObject from './TripleObject';
import { TriplePredicateProps } from './types';

const TriplePredicate: React.FC<TriplePredicateProps> = (props) => {
    const { id, value, objects, data, openTripleModal, openRemoveTripleModal } = props;

    const newData = {
        ...data,
        predicateValue: value.value,
    };

    return (
        <div className="TriplePredicateObject">
            <div className="DataSpecificationPredicate">
                <div>
                    {value.prefixedValue ? value.prefixedValue : value.value}
                </div>
            </div>

            <div className="DataSpecificationObjects">
                {objects.map(object => (
                    <TripleObject
                        key={object.id}
                        id={object.id}
                        type={object.type}
                        title={object.title}
                        description={object.description}
                        value={object.value}
                        repeated={object.repeated}
                        data={newData}
                        tripleId={object.tripleId}
                        openTripleModal={openTripleModal}
                        openRemoveTripleModal={openRemoveTripleModal}
                    />
                ))}
            </div>
        </div>
    );
};

export default TriplePredicate;
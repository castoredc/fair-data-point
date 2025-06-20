import React from 'react';
import TripleObject from './TripleObject';
import { TriplePredicateProps } from './types';
import { TableCell, TableRow } from '@mui/material';

const TriplePredicate: React.FC<TriplePredicateProps> = props => {
    const { id, value, objects, data, openTripleModal, openRemoveTripleModal } = props;

    const newData = {
        ...data,
        predicateValue: value.value,
    };

    return (
        <TableRow className="TriplePredicateObject">
            <TableCell className="DataSpecificationPredicate" sx={{ width: '50%', verticalAlign: 'top' }}>
                {value.prefixedValue ? value.prefixedValue : value.value}
            </TableCell>
            <TableCell className="DataSpecificationObjects" sx={{ width: '50%', verticalAlign: 'top' }}>
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
            </TableCell>
        </TableRow>
    );
};

export default TriplePredicate;

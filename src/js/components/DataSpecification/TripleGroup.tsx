import React from 'react';
import TriplePredicate from './TriplePredicate';
import { Node } from './Node';
import { TripleGroupProps } from './types';
import {
    TableCell,
    TableRow,
    Table,
    TableBody,
} from '@mui/material';

const TripleGroup: React.FC<TripleGroupProps> = props => {
    const { id, type, title, repeated, value, predicates, openTripleModal, openRemoveTripleModal } = props;

    const newData = {
        subjectType: type,
        subjectValue: id,
    };

    return (
        <TableRow className="DataSpecificationTriple">
            <TableCell className="DataSpecificationSubject" sx={{ verticalAlign: 'top' }}>
                {Node(title, type, value, repeated)}
            </TableCell>
            <TableCell colSpan={2} sx={{ padding: 0, border: 0, verticalAlign: 'top' }}>
                <Table size="small" sx={{ tableLayout: 'fixed' }}>
                    <TableBody className="DataSpecificationPredicateObjects">
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
                    </TableBody>
                </Table>
            </TableCell>
        </TableRow>
    );
};

export default TripleGroup;

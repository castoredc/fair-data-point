import React from 'react';
import TripleGroup from './TripleGroup';
import Button from '@mui/material/Button';
import NoResults from 'components/NoResults';

import AddIcon from '@mui/icons-material/Add';
import EditIcon from '@mui/icons-material/Edit';
import Stack from '@mui/material/Stack';
import { Paper, Table, TableBody, TableCell, TableContainer, TableHead, TableRow } from '@mui/material';

type Triple = {
    id: string;
    type: string;
    title: string;
    repeated: boolean;
    description: string;
    value: any; // Adjust type as necessary
    predicates: any[]; // Adjust type as necessary
};

type DataSpecificationModuleProps = {
    groupedTriples: Triple[];
    openModuleModal: () => void;
    openTripleModal: (triple: Triple | null) => void;
    openRemoveTripleModal: (tripleId: string) => void;
};

const DataSpecificationModule: React.FC<DataSpecificationModuleProps> = ({
                                                                             groupedTriples,
                                                                             openModuleModal,
                                                                             openTripleModal,
                                                                             openRemoveTripleModal,
                                                                         }) => {
    return (
        <div className="DataSpecificationModule">
            <div className="ButtonBar">
                <Stack direction="row" sx={{ justifyContent: 'flex-end', pb: 2 }} spacing={1}>
                    <Button startIcon={<EditIcon />} variant="outlined" onClick={openModuleModal}>
                        Edit group
                    </Button>
                    <Button
                        variant="contained"
                        startIcon={<AddIcon />}
                        onClick={() => {
                            openTripleModal(null);
                        }}
                    >
                        Add triple
                    </Button>
                </Stack>
            </div>

            <TableContainer component={Paper} className="DataSpecificationTable LargeTable">
                <Table>
                    <TableHead>
                        <TableRow className="DataSpecificationTableHeader TableHeader">
                            <TableCell sx={{ width: '33.33%' }}>Subject</TableCell>
                            <TableCell sx={{ width: '33.33%' }}>Predicate</TableCell>
                            <TableCell sx={{ width: '33.33%' }}>Object</TableCell>
                        </TableRow>
                    </TableHead>
                    <TableBody className="DataSpecificationTableBody TableBody">
                        {groupedTriples.length === 0 ? (
                            <TableRow>
                                <TableCell colSpan={3}>
                                    <NoResults>This group does not contain triples.</NoResults>
                                </TableCell>
                            </TableRow>
                        ) : (
                            groupedTriples.map(element => (
                                <TripleGroup
                                    key={element.id}
                                    id={element.id}
                                    type={element.type}
                                    title={element.title}
                                    repeated={element.repeated}
                                    value={element.value}
                                    predicates={element.predicates}
                                    openTripleModal={openTripleModal}
                                    openRemoveTripleModal={openRemoveTripleModal}
                                />
                            ))
                        )}
                    </TableBody>
                </Table>
            </TableContainer>
        </div>
    );
};

export default DataSpecificationModule;

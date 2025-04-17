import React from 'react';
import TripleGroup from './TripleGroup';
import Button from '@mui/material/Button';
import NoResults from 'components/NoResults';

import AddIcon from '@mui/icons-material/Add';
import EditIcon from '@mui/icons-material/Edit';
import Stack from '@mui/material/Stack';

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
                <Stack direction="row" sx={{ justifyContent: 'space-between' }}>
                    <Button startIcon={<EditIcon />} variant="outlined" onClick={openModuleModal}>
                        Edit group
                    </Button>
                    <Button
                        variant="outlined"
                        startIcon={<AddIcon />}
                        onClick={() => {
                            openTripleModal(null);
                        }}
                    >
                        Add triple
                    </Button>
                </Stack>
            </div>

            <div className="DataSpecificationTable LargeTable">
                <div className="DataSpecificationTableHeader TableHeader">
                    <div>
                        <div>Subject</div>
                    </div>
                    <div>
                        <div>Predicate</div>
                    </div>
                    <div>
                        <div>Object</div>
                    </div>
                </div>

                {groupedTriples.length === 0 ? (
                    <NoResults>This group does not contain triples.</NoResults>
                ) : (
                    <div className="DataSpecificationTableBody TableBody">
                        {groupedTriples.map(element => (
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
                        ))}
                    </div>
                )}
            </div>
        </div>
    );
};

export default DataSpecificationModule;

import React, { FC } from 'react';
import { Box, IconButton, TextField } from '@mui/material';
import ClearIcon from '@mui/icons-material/Clear';
import { OntologyType } from 'types/OntologyType';
import Stack from '@mui/material/Stack';

interface ConceptItem {
    code: string;
    displayName: string;
    ontology: string | OntologyType | null;
}

interface ConceptListProps {
    concepts: ConceptItem[];
    ontologies: OntologyType[];
    onRemoveConcept: (index: number) => void;
}

export const ConceptList: FC<ConceptListProps> = ({
                                                      concepts,
                                                      ontologies,
                                                      onRemoveConcept,
                                                  }) => {
    return (
        <div className="Concepts">
            {concepts.map((concept, index) => {
                const ontology =
                    typeof concept.ontology === 'string'
                        ? ontologies.find((o) => o.id === concept.ontology)
                        : concept.ontology;

                return (
                    <Stack direction="row" spacing={2} key={index}>
                        <TextField
                            defaultValue={ontology?.name ?? ''}
                            slotProps={{
                                input: {
                                    readOnly: true,
                                },
                            }}
                            sx={{ width: 150 }}
                        />
                        <TextField
                            defaultValue={concept.code}
                            slotProps={{
                                input: {
                                    readOnly: true,
                                },
                            }}
                            sx={{ width: 100 }}
                        />
                        <TextField
                            defaultValue={concept.displayName}
                            slotProps={{
                                input: {
                                    readOnly: true,
                                },
                            }}
                            sx={{ width: 284 }}
                        />
                        <Box>
                            <IconButton className="RemoveButton" onClick={() => onRemoveConcept(index)}>
                                <ClearIcon />
                            </IconButton>
                        </Box>
                    </Stack>
                );
            })}
        </div>
    );
};
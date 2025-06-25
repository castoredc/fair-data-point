import React, { FC } from 'react';
import {
    Box,
    IconButton,
    Paper,
    Table,
    TableBody,
    TableCell,
    TableContainer,
    TableHead,
    TableRow,
    Typography,
} from '@mui/material';
import ClearIcon from '@mui/icons-material/Clear';
import { OntologyType } from 'types/OntologyType';
import { AddConceptRow } from './AddConceptRow';

interface ConceptItem {
    code: string;
    displayName: string;
    ontology: string | OntologyType | null;
}

interface ConceptListProps {
    concepts: ConceptItem[];
    ontologies: OntologyType[];
    onRemoveConcept: (index: number) => void;
    onAddConcept: (concept: any, selectedOntology: OntologyType | null) => void;
}

export const ConceptList: FC<ConceptListProps> = ({
                                                      concepts,
                                                      ontologies,
                                                      onRemoveConcept,
                                                      onAddConcept,
                                                  }) => {
    return (
        <TableContainer
            component={Paper}
            variant="outlined"
            sx={{
                mb: 2,
                '& .MuiTableCell-root': {
                    py: 1.5,
                },
            }}
        >
            <Table size="small">
                <TableHead>
                    <TableRow>
                        <TableCell sx={{ fontWeight: 500, width: 150 }}>Ontology</TableCell>
                        <TableCell sx={{ fontWeight: 500, width: 100 }}>Code</TableCell>
                        <TableCell sx={{ fontWeight: 500 }}>Display Name</TableCell>
                        <TableCell align="right" sx={{ width: 70 }} />
                    </TableRow>
                </TableHead>
                <TableBody>
                    {concepts.map((concept, index) => {
                        const ontology =
                            typeof concept.ontology === 'string'
                                ? ontologies.find((o) => o.id === concept.ontology)
                                : concept.ontology;

                        return (
                            <TableRow
                                key={index}
                                sx={{
                                    '&:last-child td, &:last-child th': { border: 0 },
                                    '&:hover': {
                                        bgcolor: 'action.hover',
                                    },
                                }}
                            >
                                <TableCell>{ontology?.name ?? ''}</TableCell>
                                <TableCell>{concept.code}</TableCell>
                                <TableCell>{concept.displayName}</TableCell>
                                <TableCell align="right">
                                    <IconButton
                                        size="small"
                                        onClick={() => onRemoveConcept(index)}
                                        sx={{
                                            color: 'error.main',
                                            '&:hover': {
                                                bgcolor: 'error.lighter',
                                            },
                                        }}
                                    >
                                        <ClearIcon fontSize="small" />
                                    </IconButton>
                                </TableCell>
                            </TableRow>
                        );
                    })}
                    {concepts.length === 0 && (
                        <TableRow>
                            <TableCell colSpan={4} align="center" sx={{ py: 4 }}>
                                <Typography color="text.secondary">
                                    No concepts added yet
                                </Typography>
                            </TableCell>
                        </TableRow>
                    )}
                    <TableRow>
                        <TableCell colSpan={4} sx={{ border: 0 }}>
                            <AddConceptRow
                                ontologyOptions={ontologies}
                                onAddConcept={onAddConcept}
                            />
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>
        </TableContainer>
    );
};
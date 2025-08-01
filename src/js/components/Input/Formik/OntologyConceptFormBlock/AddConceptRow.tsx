import React, { FC, useCallback, useEffect, useState } from 'react';
import { Autocomplete, Box, Checkbox, CircularProgress, FormControlLabel, TextField } from '@mui/material';
import { isMultipleOption } from 'components/Input/Formik/Select';
import { OntologyType } from 'types/OntologyType';
import { OntologyConceptType } from 'types/OntologyConceptType';
import debounce from 'lodash/debounce';
import { apiClient } from '../../../../network';
import { OntologyConceptSearchType } from 'types/OntologyConceptSearchType';
import { useNotifications } from 'components/WithNotifications';

interface AddConceptRowProps {
    ontologyOptions: OntologyType[];
    onAddConcept: (concept: OntologyConceptSearchType | null, selectedOntology: OntologyType | null) => void;
}

/**
 * Renders the "Add new concept" row: Ontology selector + Concept Autocomplete
 */
export const AddConceptRow: FC<AddConceptRowProps> = ({
                                                          ontologyOptions,
                                                          onAddConcept,
                                                      }) => {
    const notifications = useNotifications();

    const [selectedOntology, setSelectedOntology] = useState<OntologyType | null>(null);
    const [includeIndividuals, setIncludeIndividuals] = useState<boolean>(false);

    const [conceptSearchValue, setConceptSearchValue] = useState<string>('');
    const [conceptOptions, setConceptOptions] = useState<OntologyConceptType[]>([]);
    const [loadingConcepts, setLoadingConcepts] = useState<boolean>(false);


    const fetchConceptsDebounced = React.useMemo(
        () =>
            debounce(async (searchValue: string) => {
                if (!selectedOntology || searchValue === '' || searchValue === null) {
                    setConceptOptions([]);
                    return;
                }

                setLoadingConcepts(true);

                try {
                    const response = await apiClient.get('/api/terminology/concepts', {
                        params: {
                            ontology: selectedOntology.id,
                            query: searchValue,
                            includeIndividuals: includeIndividuals,
                        },
                    });
                    setConceptOptions(response.data ?? []);
                } catch (error: any) {
                    if (error?.response?.data?.error) {
                        notifications.show(error.response.data.error, { variant: 'error' });
                    } else {
                        notifications.show('An error occurred', { variant: 'error' });
                    }
                    setConceptOptions([]);
                } finally {
                    setLoadingConcepts(false);
                }
            }, 300),
        [selectedOntology, includeIndividuals],
    );

    // Cancel any pending requests if unmount or dependencies change
    useEffect(() => {
        return () => {
            fetchConceptsDebounced.cancel();
        };
    }, [fetchConceptsDebounced]);

    const handleOntologyChange = useCallback((ontology: OntologyType | null) => {
        setSelectedOntology(ontology);
        setConceptSearchValue('');
        setConceptOptions([]);
    }, []);

    const handleConceptSearchInputChange = useCallback(
        (_event: React.SyntheticEvent<Element, Event>, newValue: string) => {
            setConceptSearchValue(newValue);
            fetchConceptsDebounced(newValue);
        },
        [fetchConceptsDebounced],
    );

    const toggleIncludeIndividuals = useCallback(() => {
        setIncludeIndividuals((prev) => !prev);
    }, []);

    return (
        <Box
            sx={{
                py: 2,
                display: 'flex',
                alignItems: 'center',
                gap: 2,
            }}
        >
            <Autocomplete
                options={ontologyOptions}
                value={selectedOntology ?? null}
                onChange={(_event: any, newValue: any) => {
                    if (!newValue) {
                        handleOntologyChange(null);
                        return;
                    }
                    // If multiple, take the first; otherwise use as is
                    const returnValue = isMultipleOption(newValue) ? newValue[0] : newValue;
                    handleOntologyChange(returnValue || null);
                }}
                getOptionLabel={(option) => option.name || ''}
                isOptionEqualToValue={(option, value) => option.id === value?.id}
                sx={{ width: 150 }}
                renderInput={(params) => <TextField {...params} size="small" placeholder="Select ontology" />}
                disableClearable
            />

            <Autocomplete
                disabled={!selectedOntology}
                options={conceptOptions}
                value={null}
                onInputChange={handleConceptSearchInputChange}
                onChange={(event: any, newValue: any) => {
                    if (!newValue) {
                        onAddConcept(null, selectedOntology);
                        return;
                    }
                    const returnValue = isMultipleOption(newValue) ? newValue[0] : newValue;
                    onAddConcept(returnValue, selectedOntology);
                }}
                getOptionLabel={(option) => option.label || ''}
                isOptionEqualToValue={(option, value) => option.code === value?.code}
                blurOnSelect
                loading={loadingConcepts}
                renderInput={(params) => (
                    <TextField
                        {...params}
                        size="small"
                        placeholder="Search concepts"
                        InputProps={{
                            ...params.InputProps,
                            endAdornment: (
                                <>
                                    {loadingConcepts ? <CircularProgress color="inherit" size={20} /> : null}
                                    {params.InputProps.endAdornment}
                                </>
                            ),
                        }}
                    />
                )}
                sx={{ width: 400 }}
            />

            <FormControlLabel
                control={
                    <Checkbox
                        name="includeIndividuals"
                        onChange={toggleIncludeIndividuals}
                        checked={includeIndividuals}
                        size="small"
                    />
                }
                label="Include individuals"
                sx={{ ml: 0 }}
            />
        </Box>
    );
};
import React, { FC, useRef } from 'react';
import { RowActionsMenu } from 'components/DataTable/RowActionsMenu';
import Box from '@mui/material/Box';

interface AnnotationProps {
    conceptCode: string;
    displayName: string;
    ontology: string;
    handleRemove: () => void;
}

const Annotation: FC<AnnotationProps> = ({ conceptCode, displayName, ontology, handleRemove }) => {
    const ref = useRef<HTMLDivElement>(null);

    return (
        <Box sx={{ display: 'flex', gap: 2, alignItems: 'center' }}>
            <Box flex={1}>{ontology}</Box>
            <Box flex={2}>{displayName}</Box>
            <Box flex={1}>{conceptCode}</Box>
            <RowActionsMenu
                items={[
                    {
                        destination: () => handleRemove(),
                        label: 'Delete annotation',
                    },
                ]}
            />
        </Box>
    );
};
export default Annotation;

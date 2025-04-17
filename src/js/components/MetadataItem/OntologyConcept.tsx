import React from 'react';

import { OntologyType } from 'types/OntologyType';
import Tooltip from '@mui/material/Tooltip';

interface OntologyConceptProps {
    code: string;
    displayName: string;
    ontology: OntologyType;
    url: string;
}

const OntologyConcept: React.FC<OntologyConceptProps> = ({ code, displayName, ontology, url }) => {
    return (
        <div className="OntologyConcept">
            <Tooltip title={`${ontology.name} · ${code}`}>
                <a href={url} target="_blank">
                    {displayName}
                </a>
            </Tooltip>
        </div>
    );
};

export default OntologyConcept;

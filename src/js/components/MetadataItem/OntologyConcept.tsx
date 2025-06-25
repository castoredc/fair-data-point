import React from 'react';

import './MetadataItem.scss';
import { OntologyType } from 'types/OntologyType';
import { Tooltip } from '@castoredc/matter';

interface OntologyConceptProps {
    code: string;
    displayName: string;
    ontology: OntologyType;
    url: string;
}

const OntologyConcept: React.FC<OntologyConceptProps> = ({ code, displayName, ontology, url }) => {
    return (
        <div className="OntologyConcept">
            <Tooltip content={`${ontology.name} · ${code}`}>
                <a href={url} target="_blank">
                    {displayName}
                </a>
            </Tooltip>
        </div>
    );
};

export default OntologyConcept;

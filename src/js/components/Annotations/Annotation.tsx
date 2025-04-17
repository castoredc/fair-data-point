import React, { FC, useRef } from 'react';
import { RowActionsMenu } from 'components/DataTable/RowActionsMenu';

interface AnnotationProps {
    conceptCode: string;
    displayName: string;
    ontology: string;
    handleRemove: () => void;
}

const Annotation: FC<AnnotationProps> = ({ conceptCode, displayName, ontology, handleRemove }) => {
    const ref = useRef<HTMLDivElement>(null);

    return (
        <div className="Annotation">
            <div className="OntologyName">{ontology}</div>
            <div className="ConceptDisplayName">{displayName}</div>
            <div className="ConceptCode">{conceptCode}</div>
            <div className="DeleteAnnotation" ref={ref}>
                <RowActionsMenu
                    items={[
                        {
                            destination: () => handleRemove(),
                            label: 'Delete annotation',
                        },
                    ]}
                />
            </div>
        </div>
    );
};
export default Annotation;

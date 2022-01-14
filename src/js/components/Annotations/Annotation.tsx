import React, {FC, useRef} from 'react'
import {ActionMenu} from "@castoredc/matter";

interface AnnotationProps {
    conceptCode: string,
    displayName: string,
    ontology: string,
    handleRemove: () => void,
}

const Annotation: FC<AnnotationProps> = ({conceptCode, displayName, ontology, handleRemove}) => {
    const ref = useRef<HTMLDivElement>(null);

    return <div className="Annotation">
        <div className="OntologyName">{ontology}</div>
        <div className="ConceptDisplayName">{displayName}</div>
        <div className="ConceptCode">{conceptCode}</div>
        <div className="DeleteAnnotation" ref={ref}>
            <ActionMenu
                accessibleLabel="Contextual menu"
                container={ref.current !== null ? ref.current : undefined}
                items={[
                    {
                        destination: () => handleRemove(),
                        label: 'Delete annotation'
                    }
                ]}
            />
        </div>
    </div>;
}
export default Annotation;
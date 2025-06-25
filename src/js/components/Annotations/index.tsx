import React, { FC } from 'react';

import Annotation from './Annotation';

interface AnnotationsProps {
    annotations: any;
    handleRemove: (annotation) => void;
}

const Annotations: FC<AnnotationsProps> = ({ annotations, handleRemove }) => {
    return (
        <div className="Annotations">
            {annotations.map(annotation => {
                return (
                    <Annotation
                        key={annotation.id}
                        conceptCode={annotation.concept.code}
                        displayName={annotation.concept.displayName}
                        ontology={annotation.concept.ontology.name}
                        handleRemove={() => handleRemove(annotation)}
                    />
                );
            })}
        </div>
    );
};
export default Annotations;

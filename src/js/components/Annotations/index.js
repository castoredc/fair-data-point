import React, {Component} from 'react'

import './Annotations.scss'
import Annotation from "./Annotation";

export default class Annotations extends Component {
    render() {
        const { annotations } = this.props;

        return <div className="Annotations">
            {annotations.map((annotation) => {
                return <Annotation
                    key={annotation.id}
                    id={annotation.id}
                    conceptCode={annotation.concept.code}
                    displayName={annotation.concept.displayName}
                    ontology={annotation.concept.ontology.name}
                />;
            })}
        </div>;
    }
}
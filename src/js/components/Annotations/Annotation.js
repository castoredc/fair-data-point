import React, {Component} from 'react'

export default class Annotation extends Component {
    render() {
        const {id, conceptCode, displayName, ontology} = this.props;

        return <div className="Annotation">
            <div className="OntologyName">{ontology}</div>
            <div className="ConceptDisplayName">{displayName}</div>
            <div className="ConceptCode">{conceptCode}</div>
        </div>;
    }
}
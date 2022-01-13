import React, {Component} from 'react'
import {ActionMenu} from "@castoredc/matter";

export default class Annotation extends Component {
    constructor(props) {
        super(props);
        this.ref = React.createRef();
    }

    render() {
        const {id, conceptCode, displayName, ontology, handleRemove} = this.props;

        return <div className="Annotation">
            <div className="OntologyName">{ontology}</div>
            <div className="ConceptDisplayName">{displayName}</div>
            <div className="ConceptCode">{conceptCode}</div>
            <div className="DeleteAnnotation" ref={this.ref}>
                <ActionMenu
                    accessibleLabel="Contextual menu"
                    // container={this.ref.current}
                    container={this.ref.current}
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
}
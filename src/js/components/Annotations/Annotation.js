import React, {Component} from 'react'

import Row from "react-bootstrap/Row";
import Col from "react-bootstrap/Col";

export default class Annotation extends Component {
    render() {
        const { id, conceptCode, displayName, ontology } = this.props;

        return <div className="Annotation">
            <Row>
                <Col sm={3}>
                    <div className="OntologyName">{ontology}</div>
                </Col>
                <Col sm={6}>
                    <div className="ConceptDisplayName">{displayName}</div>
                </Col>
                <Col sm={3}>
                    <div className="ConceptCode">{conceptCode}</div>
                </Col>
            </Row>
        </div>;
    }
}
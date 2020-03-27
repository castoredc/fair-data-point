import React, {Component} from 'react'
import Row from 'react-bootstrap/Row'
import Col from 'react-bootstrap/Col'
import GraphElementLabel from "./GraphElementLabel";
import GraphElementValue from "./GraphElementValue";

class GraphElement extends Component {
    constructor (props) {
        super(props);
    }
    render() {
        const { iri, short, children } = this.props;

        const toGraphElementValue = (item, index) => {
            return <GraphElementValue key={index} {... item} />
        };

        return <div className="GraphChild">
            <Row>
                <Col xs={2} className="GraphElementPredicate">
                    <GraphElementLabel iri={iri} short={short} />
                </Col>
                <Col xs={10}>
                    {children.length > 0 ? children.map(toGraphElementValue) :
                        <Alert variant="info">
                            <strong>No information found</strong><br/>
                        </Alert>}
                </Col>
            </Row>
        </div>
    }
}

export default GraphElement

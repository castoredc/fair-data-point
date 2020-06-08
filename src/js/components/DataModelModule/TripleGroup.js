import React, {Component} from 'react'
import {Col, Row} from "react-bootstrap";
import {Icon} from "@castoredc/matter";

export default class TripleGroup extends Component {
    render() {
        const { id, type, title, description, value, predicates } = this.props;

        return <Row className="DataModelTriple">
            <Col sm={4}>
                {Node(title, type, value)}
            </Col>
            <Col sm={8}>
                {predicates.map((predicate) => {
                    return <TriplePredicate key={predicate.id} id={predicate.id} value={predicate.value} objects={predicate.objects} />;
                })}
            </Col>
        </Row>;
    }
}

export class TriplePredicate extends Component {
    render() {
        const { id, value, objects } = this.props;

        return <Row className="TriplePredicate">
            <Col sm={6}>
                {value.prefixedValue ? value.prefixedValue : value.value}
            </Col>
            <Col sm={6}>
                {objects.map((object) => {
                    return <TripleObject key={object.id} id={object.id} type={object.type} title={object.title} description={object.description} value={object.value} />;
                })}
            </Col>
        </Row>;
    }
}

export class TripleObject extends Component {
    render() {
        const { id, type, title, description, value } = this.props;

        return <div className="TripleObject">
            {Node(title, type, value)}
        </div>;
    }
}

export const Node = (title, type, value) => {
    return <div>
        {title}
        {(type === "internal") && <div className="NodeInfo Slug">
            <span>/{value}</span>
        </div>}
        {(type === "external") && <div className="NodeInfo PrefixedUri">
            <span>
                {value.prefixedValue !== null ? value.prefixedValue : `...:${value.base}`}
                &nbsp;<Icon type="openNewWindow" width="8px" height="8px" />
            </span>
        </div>}
        {(type === "value") && <div className="NodeInfo Value">
            <span>
                {value.value === 'annotated' ? 'Annotated value' : `Plain value (${value.dataType})`}
            </span>
        </div>}
        {(type === "literal") && <div className="NodeInfo Literal">
            <span>
                {value.value} (${value.dataType})
            </span>
        </div>}
    </div>;
};
import React, {Component} from 'react'
import {Col, Row} from "react-bootstrap";
import Toggle from "../Toggle";
import Icon from "../Icon";
import Container from "react-bootstrap/Container";
import TripleGroup from "./TripleGroup";
import {Button} from "@castoredc/matter";

export default class DataModelModule extends Component {
    render() {
        const { title, order, groupedTriples, openModal } = this.props;

        return <div className="DataModelModule">
            <Toggle title={`Module ${order}. ${title}`}>
                <Row className="DataModelTableHeader">
                    <Col sm={4}>Subject</Col>
                    <Col sm={4}>Predicate</Col>
                    <Col sm={4}>Object</Col>
                </Row>
                <Row className="DataModelTableBody">
                    {groupedTriples.length === 0 ? <Col sm={12} className="NoResults">This module does not contain triples.</Col> : <Container>
                        {groupedTriples.map((element) => {
                            return <TripleGroup
                                key={element.id}
                                id={element.id}
                                type={element.type}
                                title={element.title}
                                description={element.description}
                                value={element.value}
                                predicates={element.predicates} />;
                        })}
                    </Container>
                    }
                </Row>
                <Row className="DataModelModuleAddTriple">
                    <Col sm={12}>
                        {/*<Button variant="primary" className="AddButton" onClick={openModal}><Icon type="add" /> Add triple</Button>*/}
                        <Button icon="add" onClick={openModal}>Add triple</Button>
                    </Col>
                </Row>
            </Toggle>
        </div>;
    }
}
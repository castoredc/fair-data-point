import React, {Component} from 'react'
import {Col, Row} from "react-bootstrap";
import Toggle from "../Toggle";
import Icon from "../Icon";
import Container from "react-bootstrap/Container";
import TripleGroup from "./TripleGroup";
import {Button, Stack} from "@castoredc/matter";

export default class DataModelModule extends Component {
    render() {
        const { title, order, groupedTriples, openModuleModal, openTripleModal, openRemoveTripleModal } = this.props;

        return <div className="DataModelModule">
            <Toggle title={`Module ${order}. ${title}`}>
                <div className="ButtonBar">
                    <Stack alignment="end" distribution="trailing">
                        <Button icon="edit" buttonType="secondary" onClick={openModuleModal}>
                            Edit module
                        </Button>
                        <Button icon="add" onClick={() => {openTripleModal(null)}}>
                            Add triple
                        </Button>
                    </Stack>
                </div>

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
                                predicates={element.predicates}
                                openTripleModal={openTripleModal}
                                openRemoveTripleModal={openRemoveTripleModal}
                            />;
                        })}
                    </Container>
                    }
                </Row>
            </Toggle>
        </div>;
    }
}
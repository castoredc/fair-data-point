import React, {Component} from 'react'
import axios from "axios";
import {toast} from "react-toastify";
import ToastContent from "../ToastContent";
import Tab from "react-bootstrap/Tab";
import StudyStructure from "./StudyStructure";
import Row from "react-bootstrap/Row";
import Col from "react-bootstrap/Col";
import Nav from "react-bootstrap/Nav";
import Button from "react-bootstrap/Button";
import Container from "react-bootstrap/Container";
import {Redirect} from "react-router-dom";
import InlineLoader from "../LoadingScreen/InlineLoader";

export default class RDFStudyStructure extends Component {
    render() {
        const { studyId, handleSelect, selected, structure } = this.props;

        const selection = selected !== null ? [{ type: 'fieldId', value: selected.id }] : [];

        return <div>
            <Container>
                <Tab.Container id="csv-study-structure" defaultActiveKey="study">
                    <Row className="StudyStructureTabs">
                        <Col sm={3} />
                        <Col sm={9}>
                            <Nav variant="tabs">
                                <Nav.Item>
                                    <Nav.Link eventKey="study" disabled={structure.study.length === 0}>Study</Nav.Link>
                                </Nav.Item>
                                <Nav.Item>
                                    <Nav.Link eventKey="report" disabled={structure.report.length === 0}>Reports</Nav.Link>
                                </Nav.Item>
                                <Nav.Item>
                                    <Nav.Link eventKey="survey" disabled={structure.survey.length === 0}>Surveys</Nav.Link>
                                </Nav.Item>
                            </Nav>
                        </Col>
                    </Row>
                    <Row className="StudyStructureContents">
                        <Col sm={12}>
                            <Tab.Content>
                                <Tab.Pane eventKey="study">
                                    {structure.study.length > 0 && <StudyStructure
                                        selectable onSelect={handleSelect} selection={selection}
                                        studyId={studyId} contents={structure.study}
                                    />}
                                </Tab.Pane>
                                <Tab.Pane eventKey="report">
                                    {structure.report.length > 0 && <StudyStructure
                                        selectable onSelect={handleSelect} selection={selection}
                                        studyId={studyId} contents={structure.report} />}
                                </Tab.Pane>
                                <Tab.Pane eventKey="survey">
                                    {structure.survey.length > 0 && <StudyStructure
                                        selectable onSelect={handleSelect} selection={selection}
                                        studyId={studyId} contents={structure.survey} />}
                                </Tab.Pane>
                            </Tab.Content>
                        </Col>
                    </Row>
                </Tab.Container>
            </Container>
        </div>
    }

}
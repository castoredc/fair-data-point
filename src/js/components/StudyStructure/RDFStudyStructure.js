import React, {Component} from 'react'
import StudyStructure from "./StudyStructure";
import {Tabs} from "@castoredc/matter";

export default class RDFStudyStructure extends Component {
    constructor(props) {
        super(props);
        this.state = {
            selectedType:         props.mapping.node.repeated ? 'report' : 'study',
        };
    }

    handleSelect = (event, data, selected) => {
        const { mapping, onSelect } = this.props;

        onSelect({
            node: mapping.node.id,
            element: data.id
        });
    };

    render() {
        const { studyId, structure, mapping, onSelect } = this.props;
        const {selectedType} = this.state;

        return <div className="RDFStudyStructure">
                <Tabs
                    onChange={this.changeTab}
                    selected={selectedType}
                    tabs={{
                        study: {
                            title: 'Study',
                            content: <StudyStructure
                                onSelect={this.handleSelect} selection={[]}
                                studyId={studyId} contents={structure.study}
                                dataFormat={mapping.node.value.value}
                                dataType={mapping.node.value.dataType}
                            />,
                        },
                        report: {
                            title: 'Reports',
                            content: <StudyStructure
                                onSelect={this.handleSelect} selection={[]}
                                studyId={studyId} contents={structure.report}
                                dataFormat={mapping.node.value.value}
                                dataType={mapping.node.value.dataType}
                            />,
                        },
                        survey: {
                            title: 'Surveys',
                            content: <StudyStructure
                                onSelect={this.handleSelect} selection={[]}
                                studyId={studyId} contents={structure.survey}
                                dataFormat={mapping.node.value.value}
                                dataType={mapping.node.value.dataType}
                            />,
                        },
                    }}
                />
            </div>


            {/*<Tab.Container id="csv-study-structure" defaultActiveKey={mapping.node.repeated ? 'report' : 'study'}>*/}
            {/*    <Row className="TabTabs StudyStructureTabs">*/}
            {/*        <Col sm={3} />*/}
            {/*        <Col sm={9}>*/}
            {/*            <Nav variant="tabs">*/}
            {/*                {! mapping.node.repeated && <Nav.Item>*/}
            {/*                    <Nav.Link eventKey="study" disabled={structure.study.length === 0}>Study</Nav.Link>*/}
            {/*                </Nav.Item> }*/}
            {/*                <Nav.Item>*/}
            {/*                    <Nav.Link eventKey="report" disabled={structure.report.length === 0}>Reports</Nav.Link>*/}
            {/*                </Nav.Item>*/}
            {/*                <Nav.Item>*/}
            {/*                    <Nav.Link eventKey="survey" disabled={structure.survey.length === 0}>Surveys</Nav.Link>*/}
            {/*                </Nav.Item>*/}
            {/*            </Nav>*/}
            {/*        </Col>*/}
            {/*    </Row>*/}
            {/*    <Row className="StudyStructureContents">*/}
            {/*        <Col sm={12}>*/}
            {/*            <Tab.Content>*/}
            {/*                {! mapping.node.repeated && <Tab.Pane eventKey="study">*/}
            {/*                    {structure.study.length > 0 && <StudyStructure*/}
            {/*                        onSelect={this.handleSelect} selection={[]}*/}
            {/*                        studyId={studyId} contents={structure.study}*/}
            {/*                        dataFormat={mapping.node.value.value}*/}
            {/*                        dataType={mapping.node.value.dataType}*/}
            {/*                    />}*/}
            {/*                </Tab.Pane>}*/}
            {/*                <Tab.Pane eventKey="report">*/}
            {/*                    {structure.report.length > 0 && <StudyStructure*/}
            {/*                        onSelect={this.handleSelect} selection={[]}*/}
            {/*                        studyId={studyId} contents={structure.report}*/}
            {/*                        dataFormat={mapping.node.value.value}*/}
            {/*                        dataType={mapping.node.value.dataType}*/}
            {/*                    />}*/}
            {/*                </Tab.Pane>*/}
            {/*                <Tab.Pane eventKey="survey">*/}
            {/*                    {structure.survey.length > 0 && <StudyStructure*/}
            {/*                        onSelect={this.handleSelect} selection={[]}*/}
            {/*                        studyId={studyId} contents={structure.survey}*/}
            {/*                        dataFormat={mapping.node.value.value}*/}
            {/*                        dataType={mapping.node.value.dataType}*/}
            {/*                    />}*/}
            {/*                </Tab.Pane>*/}
            {/*            </Tab.Content>*/}
            {/*        </Col>*/}
            {/*    </Row>*/}
            {/*</Tab.Container>*/}
    }

}
import React, {Component} from 'react'
import axios from "axios";
import {toast} from "react-toastify";
import ToastContent from "../ToastContent";
import Tab from "react-bootstrap/Tab";
import StudyStructure from "./StudyStructure";
import Row from "react-bootstrap/Row";
import Col from "react-bootstrap/Col";
import Nav from "react-bootstrap/Nav";
import Container from "react-bootstrap/Container";
import {Redirect} from "react-router-dom";
import InlineLoader from "../LoadingScreen/InlineLoader";
import {ValidatorForm} from "react-form-validator-core";
import {Button} from "@castoredc/matter";

export default class CSVStudyStructure extends Component {
    constructor(props) {
        super(props);
        this.state = {
            isLoadingStructure:   true,
            hasLoadedStructure:   false,
            structure:            null,
            distributionContents: props.distributionContents,
            submitDisabled:       false,
            isSaved:              false
        };
    }

    componentDidMount() {
        this.getStructure();
    }

    getStructure = () => {
        const { studyId } = this.props;

        this.setState({
            isLoadingStructure: true,
        });

        axios.get('/api/castor/study/' + studyId + '/structure/')
            .then((response) => {
                this.setState({
                    structure:          response.data,
                    isLoadingStructure: false,
                    hasLoadedStructure: true,
                });
            })
            .catch((error) => {
                if (error.response && typeof error.response.data.error !== "undefined") {
                    toast.error(<ToastContent type="error" message={error.response.data.error}/>);
                } else {
                    toast.error(<ToastContent type="error" message="An error occurred"/>);
                }

                this.setState({
                    isLoadingStructure: false,
                });
            });
    };

    handleSelect = (event, field, selectValue) => {
        if(event.target.tagName.toUpperCase() !== 'INPUT') {
            let { distributionContents } = this.state;

            distributionContents = distributionContents.filter(({type, value}) => {
                return !((type === 'fieldId' && value === field.id) || (type === 'variableName' && value === field.variableName))
            });

            if (selectValue === true) {
                distributionContents.push({type: 'fieldId', value: field.id});
            }

            this.setState({
                distributionContents: distributionContents,
            });
        }
    };

    saveDistribution = () => {
        const { catalog, dataset, distribution } = this.props;
        const { distributionContents } = this.state;

        this.setState({
            submitDisabled: true
        });

        axios.post('/api/dataset/' + dataset + '/distribution/' + distribution + '/contents', distributionContents)
            .then(() => {
                this.setState({
                    isSaved: true,
                    submitDisabled: false
                });
            })
            .catch((error) => {
                const message = (error.response && typeof error.response.data.error !== "undefined") ? error.response.data.error : 'An error occurred while saving the distribution';
                toast.error(<ToastContent type="error" message={message}/>);

                this.setState({
                    submitDisabled: false
                });
            });
    };

    render() {
        const { studyId, catalog, dataset, distribution } = this.props;
        const { structure, distributionContents, submitDisabled, isSaved } = this.state;

        if (!this.state.hasLoadedStructure) {
            return <InlineLoader />;
        }

        if (isSaved) {
            return <Redirect push to={'/admin/dataset/' + dataset + '/distribution/' + distribution} />;
        }

        return <div>
            <Tab.Container id="csv-study-structure" defaultActiveKey="study">
                <Row className="TabTabs StudyStructureTabs">
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
                        <ValidatorForm
                            ref={node => (this.form = node)}
                            onSubmit={this.saveDistribution}
                        >
                            <Tab.Content>
                                <Tab.Pane eventKey="study">
                                    {structure.study.length > 0 && <StudyStructure
                                        selectable onSelect={this.handleSelect} selection={distributionContents}
                                        studyId={studyId} contents={structure.study}
                                    />}
                                </Tab.Pane>
                                <Tab.Pane eventKey="report">
                                    {structure.report.length > 0 && <StudyStructure
                                        selectable onSelect={this.handleSelect} selection={distributionContents}
                                        studyId={studyId} contents={structure.report} />}
                                </Tab.Pane>
                                <Tab.Pane eventKey="survey">
                                    {structure.survey.length > 0 && <StudyStructure
                                        selectable onSelect={this.handleSelect} selection={distributionContents}
                                        studyId={studyId} contents={structure.survey} />}
                                </Tab.Pane>
                            </Tab.Content>
                        </ValidatorForm>
                    </Col>
                </Row>
            </Tab.Container>

            <Row className="StudyStructureFooter">
                <Col sm={3} />
                <Col sm={9} className="SaveButton">
                    <span className="FieldCount">{distributionContents.length} field{distributionContents.length !== 1 && 's'} selected</span>
                    <Button onClick={this.saveDistribution} disabled={submitDisabled}>Save distribution</Button>
                </Col>
            </Row>
        </div>
    }

}
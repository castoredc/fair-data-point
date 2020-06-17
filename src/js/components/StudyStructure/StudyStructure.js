import React, {Component} from 'react'
import Col from "react-bootstrap/Col";
import Row from "react-bootstrap/Row";
import './StudyStructure.scss';
import Button from "react-bootstrap/Button";
import {classNames} from "../../util";
import InlineLoader from "../LoadingScreen/InlineLoader";
import axios from "axios";
import {toast} from "react-toastify";
import ToastContent from "../ToastContent";
import FieldListItem from "../ListItem/FieldListItem";
import Container from "react-bootstrap/Container";

export default class StudyStructure extends Component {
    constructor(props) {
        super(props);
        this.state = {
            isLoadingFields: true,
            fields:          [],
            selectedStep:    null
        };
    }

    componentDidMount() {
        const { contents } = this.props;
        this.handleStepSwitch(contents[0].steps[0]);
    }

    handleStepSwitch = (step) => {
        const { studyId } = this.props;
        const { selectedStep } = this.state;

        if(selectedStep === null || step.id !== selectedStep.id) {
            this.setState({
                isLoadingFields: true,
            });

            axios.get('/api/castor/study/' + studyId + '/structure/step/' + step.id + '/fields')
                .then((response) => {
                    this.setState({
                        fields:          response.data,
                        isLoadingFields: false,
                        selectedStep:    step
                    });
                })
                .catch((error) => {
                    if (error.response && typeof error.response.data.error !== "undefined") {
                        toast.error(<ToastContent type="error" message={error.response.data.error}/>);
                    } else {
                        toast.error(<ToastContent type="error" message="An error occurred"/>);
                    }

                    this.setState({
                        isLoadingFields: false,
                    });
                });
        }
    };

    render() {
        const { contents, selectable, selection, onSelect, dataFormat, dataType } = this.props;
        const { isLoadingFields, fields, selectedStep } = this.state;

        return <Row>
            <Col sm={3}>
                <div className="StructureElements">
                    {contents.map((structureElement) => {
                          return <div key={structureElement.id} className="StructureElement">
                              <div className="StructureElementName">
                                  {structureElement.name}
                              </div>
                              <div className="Steps">
                                  {structureElement.steps.map((step) => {
                                      const active = selectedStep !== null && selectedStep.id === step.id;
                                      return <Button variant="link" key={step.id}
                                                     className={classNames('Step', active && 'active')}
                                                     onClick={() => {this.handleStepSwitch(step)}}
                                                     >
                                          {step.position}. {step.name}
                                      </Button>;
                                  })}
                              </div>
                          </div>;
                    })}
                </div>
            </Col>
            <Col sm={9}>
                {isLoadingFields ? <InlineLoader /> : <div className="Fields">
                    {fields.map((field) => {
                        const selected = selection.filter(({type, value}) => {
                            return (type === 'fieldId' && value === field.id) || (type === 'variableName' && value === field.variableName)
                        }).length > 0;

                        return <FieldListItem
                            selectable={selectable}
                            selected={selected}
                            onSelect={onSelect}
                            key={field.id}
                            id={field.id}
                            type={field.type}
                            label={field.label}
                            stepNumber={selectedStep.position}
                            number={field.number}
                            variableName={field.variableName}
                            exportable={field.exportable}
                            dataFormat={dataFormat}
                            dataType={dataType}
                        />;
                    })}
                </div>}
            </Col>
        </Row>
    }

}
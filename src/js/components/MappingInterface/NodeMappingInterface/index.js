import React, {Component} from 'react'
import {ChoiceOption, Heading, List, ListItem, Stack} from "@castoredc/matter";
import StudyStructure from "../../StudyStructure/StudyStructure";
import axios from "axios";
import {toast} from "react-toastify";
import ToastContent from "../../ToastContent";
import NodeMappingInterfaceFooter from "./NodeMappingInterfaceFooter";
import TwigEditor from "../../Input/CodeEditor/TwigEditor";

export default class NodeMappingInterface extends Component {
    constructor(props) {
        super(props);
        this.state = {
            selectedElements: (props.mapping && props.mapping.elements) ? props.mapping.elements : [],
            dataTransformation: props.mapping ? props.mapping.transformed : false,
            transformSyntax: props.mapping ? (props.mapping.transformed ? props.mapping.syntax : '') : '',
            step: 'elements',
        };
    }

    componentDidUpdate(prevProps, prevState, snapshot) {
        const {mapping, type} = this.props;

        if (prevProps.mapping === mapping) {
            return;
        }

        this.setState({
            selectedElements: (mapping && mapping.elements) ? mapping.elements : [],
            dataTransformation: mapping ? mapping.transformed : false,
            step: 'elements',
            transformSyntax: '',
        });
    }

    handleSelect = (fieldId, variableName, label) => {
        const {selectedElements, dataTransformation} = this.state;

        let newSelectedElements = selectedElements;
        const field = {id: fieldId, variableName, label};
        const index = selectedElements.findIndex((selectedElement) => selectedElement.id === fieldId);

        if (index !== -1) {
            newSelectedElements.splice(index, 1);
        } else if (dataTransformation) {
            newSelectedElements.push(field);
        } else {
            newSelectedElements = [field];
        }

        this.setState({
            selectedElements: newSelectedElements,
        });
    }

    handleChange = () => {
        const {dataTransformation, selectedElements} = this.state;

        const newDataTransformation = !dataTransformation;

        this.setState({
            dataTransformation: newDataTransformation,
            selectedElements: newDataTransformation ? selectedElements : [],
        })
    }

    handleSyntaxChange = (syntax) => {
        this.setState({
            transformSyntax: syntax
        })
    }

    setStep = (step) => {
        const {selectedElements, dataTransformation} = this.state;

        if (dataTransformation && selectedElements.length > 0) {
            this.setState({
                step: step,
            });
        }
    }

    handleSubmit = () => {
        const {selectedElements, dataTransformation, transformSyntax} = this.state;
        const {mapping, dataset, distribution, onSave, versionId} = this.props;

        this.setState({
            isLoading: true
        });

        axios.post('/api/dataset/' + dataset + '/distribution/' + distribution.slug + '/contents/rdf/v/' + versionId + '/node', {
            type: 'node',
            node: mapping.node.id,
            transform: dataTransformation,
            elements: selectedElements.map((element) => element.id),
            ...dataTransformation && {transformSyntax: transformSyntax},
        })
            .then((response) => {
                this.setState({
                    isLoading: false
                }, () => {
                    toast.success(<ToastContent type="success" message="The mapping was successfully saved."/>, {
                        position: "top-right",
                    });
                    onSave();
                });
            })
            .catch((error) => {
                this.setState({
                    isLoading: false
                });

                const message = (error.response && typeof error.response.data.error !== "undefined") ? error.response.data.error : 'An error occurred while saving the mapping';
                toast.error(<ToastContent type="error" message={message}/>);
            });
    }

    render() {
        const {studyId, mapping, type} = this.props;
        const {selectedElements, isLoading, dataTransformation, step, transformSyntax} = this.state;

        let valueDescription = '';
        const fieldDescription = <span>{mapping.node.repeated && <b>repeated</b>} fields</span>;

        if (mapping.node.value.value === 'plain') {
            valueDescription = 'a plain ' + mapping.node.value.dataType + ' value';
        } else if (mapping.node.value.value === 'annotated') {
            valueDescription = 'an annotated value';
        }

        const types = [
            ...mapping.node.repeated ? [] : ['study'],
            'report',
            'survey'
        ]

        const dataFormat = mapping.node.value.value;
        const dataType = mapping.node.value.dataType;

        const variables = selectedElements.map((element) => {
            if(element.variableName) {
                return element.variableName;
            }

            return element.id;
        })

        return <>
            <Stack distribution="equalSpacing">
                <Heading type="Panel">
                    {`${(mapping.elements ? `Edit` : `Add`)} mapping for ${mapping.node.title}`}
                </Heading>

                {step === 'elements' && <div className="CheckboxFormGroup HeadingCheckboxFormGroup">
                    <ChoiceOption
                        labelText="Transform data"
                        checked={dataTransformation}
                        onChange={this.handleChange}
                    />
                </div>}
            </Stack>

            <div style={{overflow: 'auto', flex: 1, display: (step === 'elements' ? 'block' : 'none')}}>
                <StudyStructure
                    studyId={studyId}
                    onSelect={this.handleSelect}
                    selection={selectedElements.map((element) => element.id)}
                    dataFormat={dataFormat}
                    dataType={dataType}
                    types={types}
                    dataTransformation={dataTransformation}
                />
            </div>

            <div style={{overflow: 'auto', flex: 1, display: (step === 'syntax' ? 'block' : 'none')}}>
                <span>
                    The data will be transformed to <b>{valueDescription}</b>.
                </span>

                <List>
                    {variables.map((variable) => {
                        return <ListItem key={variable}>{variable}</ListItem>
                    })}
                </List>

                <TwigEditor label="Twig template" value={transformSyntax} onChange={this.handleSyntaxChange}/>
            </div>

            <NodeMappingInterfaceFooter dataTransformation={dataTransformation} step={step}
                                        fieldDescription={fieldDescription} valueDescription={valueDescription}
                                        selectedElements={selectedElements} isLoading={isLoading}
                                        setStep={this.setStep} handleSubmit={this.handleSubmit}
            />
        </>
    }
}
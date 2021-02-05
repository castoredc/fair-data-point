import React, {Component} from 'react'
import {Button, ChoiceOption, Heading, Stack} from "@castoredc/matter";
import StudyStructure from "../../StudyStructure/StudyStructure";
import axios from "axios";
import {toast} from "react-toastify";
import ToastContent from "../../ToastContent";

export default class NodeMappingInterface extends Component {
    constructor(props) {
        super(props);
        this.state = {
            selectedElements: [],
            dataTransformation: false,
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
        });
    }

    handleSelect = (fieldId, variableName, label) => {
        const {selectedElements, dataTransformation} = this.state;

        let newSelectedElements = selectedElements;
        const field = {id: fieldId, variableName, label};
        const index = selectedElements.indexOf(field);

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

    setStep = (step) => {
        const {selectedElements, dataTransformation} = this.state;

        if (dataTransformation && selectedElements.length > 0) {
            this.setState({
                step: step,
            });
        }
    }

    handleSubmit = () => {
        const {selectedElements, dataTransformation} = this.state;
        const {mapping, dataset, distribution, onSave, versionId} = this.props;

        this.setState({
            isLoading: true
        });

        axios.post('/api/dataset/' + dataset + '/distribution/' + distribution.slug + '/contents/rdf/v/' + versionId + '/node', {
            type: 'node',
            node: mapping.node.id,
            transform: dataTransformation,
            elements: selectedElements.map((element) => element.id),
            ...dataTransformation && {transformSyntax: ''},
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
        const {structure, selectedElements, isLoading, dataTransformation, step} = this.state;

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
                />
            </div>
            <div style={{overflow: 'auto', flex: 1, display: (step === 'syntax' ? 'block' : 'none')}}>
                <span>
                    The data will be transformed to <b>{valueDescription}</b>.
                </span>
            </div>


            {!dataTransformation && <div className="FormButtons">
                <Stack distribution="equalSpacing">
                         <span>
                            Only {fieldDescription} supporting <b>{valueDescription}</b> can be selected.
                        </span>
                    <Stack distribution="trailing" alignment="end">
                            <span className="FieldCount">
                                {selectedElements.length} field{selectedElements.length !== 1 && 's'} selected
                            </span>

                        <Button onClick={this.handleSubmit} disabled={(isLoading || selectedElements.length === 0)}>
                            Save mapping
                        </Button>
                    </Stack>
                </Stack>
            </div>}

            {dataTransformation && <div className="FormButtons">
                {step === 'elements' && <Stack distribution="equalSpacing">
                            <span>
                                The data will be transformed to <b>{valueDescription}</b>.
                            </span>
                    <Stack distribution="trailing" alignment="end">
                                <span className="FieldCount">
                                    {selectedElements.length} field{selectedElements.length !== 1 && 's'} selected
                                </span>

                        <Button
                            onClick={() => this.setStep('syntax')}
                            disabled={(isLoading || selectedElements.length === 0)}>
                            Next
                        </Button>
                    </Stack>
                </Stack>
                }
                {step === 'syntax' && <Stack distribution="equalSpacing">
                    <Button
                        buttonType="secondary"
                        onClick={() => this.setStep('elements')}
                        disabled={(isLoading)}>
                        Back
                    </Button>

                    <Button onClick={this.handleSubmit} disabled={(isLoading || selectedElements.length === 0)}>
                        Save mapping
                    </Button>
                </Stack>}
            </div>}
        </>
    }
}
import React, { Component } from 'react';
import { ChoiceOption, Heading, List, ListItem, Stack } from '@castoredc/matter';
import StudyStructure from '../../StudyStructure';
import { toast } from 'react-toastify';
import ToastItem from 'components/ToastItem';
import NodeMappingInterfaceFooter from './NodeMappingInterfaceFooter';
import TwigEditor from '../../Input/CodeEditor/TwigEditor';
import { apiClient } from 'src/js/network';

interface NodeMappingInterfaceProps {
    studyId: string;
    dataset: string;
    distribution: any;
    versionId: string;
    mapping: any;
    onSave: () => void;
}

interface NodeMappingInterfaceState {
    selectedElements: any[];
    dataTransformation: boolean;
    transformSyntax: string;
    step: 'elements' | 'syntax';
    isLoading: boolean;
}

export default class NodeMappingInterface extends Component<NodeMappingInterfaceProps, NodeMappingInterfaceState> {
    constructor(props: NodeMappingInterfaceProps) {
        super(props);
        this.state = {
            selectedElements: props.mapping?.elements || [],
            dataTransformation: props.mapping?.transformed || false,
            transformSyntax: props.mapping?.transformed ? props.mapping.syntax : '',
            step: 'elements',
            isLoading: false,
        };
    }

    componentDidUpdate(prevProps: NodeMappingInterfaceProps) {
        const { mapping } = this.props;

        if (prevProps.mapping === mapping) {
            return;
        }

        this.setState({
            selectedElements: mapping?.elements || [],
            dataTransformation: mapping?.transformed || false,
            step: 'elements',
            transformSyntax: '',
        });
    }

    handleSelect = (fieldId: string, variableName: string, label: string) => {
        const { selectedElements, dataTransformation } = this.state;

        let newSelectedElements = [...selectedElements];
        const field = { id: fieldId, variableName, label };
        const index = selectedElements.findIndex(
            selectedElement => selectedElement.id === fieldId
        );

        if (index !== -1) {
            newSelectedElements.splice(index, 1);
        } else if (dataTransformation) {
            newSelectedElements.push(field);
        } else {
            newSelectedElements = [field];
        }

        this.setState({ selectedElements: newSelectedElements });
    };

    handleChange = () => {
        const { dataTransformation, selectedElements } = this.state;
        const newDataTransformation = !dataTransformation;

        this.setState({
            dataTransformation: newDataTransformation,
            selectedElements: newDataTransformation ? selectedElements : [],
        });
    };

    handleSyntaxChange = (syntax: string) => {
        this.setState({ transformSyntax: syntax });
    };

    setStep = (step: 'elements' | 'syntax') => {
        const { selectedElements, dataTransformation } = this.state;

        if (dataTransformation && selectedElements.length > 0) {
            this.setState({ step });
        }
    };

    handleSubmit = () => {
        const { selectedElements, dataTransformation, transformSyntax } = this.state;
        const { mapping, dataset, distribution, versionId, onSave } = this.props;

        this.setState({ isLoading: true });

        apiClient
            .post(
                `/api/dataset/${dataset}/distribution/${distribution.slug}/contents/rdf/v/${versionId}/node`,
                {
                    type: 'node',
                    node: mapping.node.id,
                    transform: dataTransformation,
                    elements: selectedElements.map(element => element.id),
                    ...(dataTransformation && { transformSyntax }),
                }
            )
            .then(() => {
                this.setState({ isLoading: false }, () => {
                    toast.success(<ToastItem type="success" title="The mapping was successfully saved." />, {
                        position: 'top-right',
                    });
                    onSave();
                });
            })
            .catch(error => {
                this.setState({ isLoading: false });

                const message = error.response?.data?.error || 'An error occurred while saving the mapping';
                toast.error(<ToastItem type="error" title={message} />);
            });
    };

    render() {
        const { studyId, mapping } = this.props;
        const { selectedElements, isLoading, dataTransformation, step, transformSyntax } = this.state;

        let valueDescription = '';
        const fieldDescription = <span>{mapping.node.repeated && <b>repeated</b>} fields</span>;

        if (mapping.node.value.value === 'plain') {
            valueDescription = `a plain ${mapping.node.value.dataType} value`;
        } else if (mapping.node.value.value === 'annotated') {
            valueDescription = 'an annotated value';
        }

        const types = [...(mapping.node.repeated ? [] : ['study']), 'report', 'survey'];

        const dataFormat = mapping.node.value.value;
        const dataType = mapping.node.value.dataType;

        const variables = selectedElements.map(element => {
            return element.variableName || element.slug || element.id;
        });

        return (
            <>
                <Stack distribution="equalSpacing">
                    <Heading type="Panel">{`${mapping.elements ? 'Edit' : 'Add'} mapping for ${mapping.node.title}`}</Heading>

                    {step === 'elements' && (
                        <div className="CheckboxFormGroup HeadingCheckboxFormGroup">
                            <ChoiceOption labelText="Transform data" checked={dataTransformation} onChange={this.handleChange} />
                        </div>
                    )}
                </Stack>

                <div
                    style={{
                        overflow: 'auto',
                        flex: 1,
                        display: step === 'elements' ? 'block' : 'none',
                    }}
                >
                    <StudyStructure
                        studyId={studyId}
                        onSelect={this.handleSelect}
                        selection={selectedElements.map(element => element.id)}
                        dataFormat={dataFormat}
                        dataType={dataType}
                        types={types}
                        dataTransformation={dataTransformation}
                    />
                </div>

                <div
                    style={{
                        overflow: 'auto',
                        flex: 1,
                        display: step === 'syntax' ? 'block' : 'none',
                    }}
                >
                    <span>
                        The data will be transformed to <b>{valueDescription}</b>.
                    </span>

                    <List>
                        {variables.map(variable => (
                            <ListItem key={variable}>{variable}</ListItem>
                        ))}
                    </List>

                    <TwigEditor label="Twig template" value={transformSyntax} onChange={this.handleSyntaxChange} />
                </div>

                <NodeMappingInterfaceFooter
                    dataTransformation={dataTransformation}
                    step={step}
                    fieldDescription={fieldDescription}
                    valueDescription={valueDescription}
                    selectedElements={selectedElements}
                    isLoading={isLoading}
                    setStep={this.setStep}
                    handleSubmit={this.handleSubmit}
                />
            </>
        );
    }
}
import React, { Component } from 'react';
import Button from '@mui/material/Button';
import AddIcon from '@mui/icons-material/Add';
import './DependencyEditor.scss';
import QueryBuilder from 'react-querybuilder';
import { RuleGroup } from '../QueryBuilder/RuleGroup';
import { Rule } from '../QueryBuilder/Rule';
import { NodeType, NodeValueType } from 'types/NodeType';
import { PrefixType } from 'types/PrefixType';
import { DependencyGroupType } from 'types/ModuleType';
import { RuleGroupType } from 'react-querybuilder/types/types';
import { ValueEditor } from 'components/QueryBuilder/ValueEditor';
import CombinatorSelector from 'components/QueryBuilder/CombinatorSelector';
import FieldSelector from 'components/QueryBuilder/FieldSelector';
import OperatorSelector from 'components/QueryBuilder/OperatorSelector';
import DeleteIcon from '@mui/icons-material/Delete';

type ModuleDependencyEditorProps = {
    modelType: string;
    valueNodes: NodeType[];
    prefixes: PrefixType[];
    value: DependencyGroupType | null;
    handleChange: (query) => void;
    save: (valid) => void;
};

type ModuleDependencyEditorState = {
    fields: { name: string; label: string; dataType: NodeValueType['dataType']; valueType: NodeValueType['value'] }[];
};

class ModuleDependencyEditor extends Component<ModuleDependencyEditorProps, ModuleDependencyEditorState> {
    constructor(props) {
        super(props);

        this.state = {
            fields: props.valueNodes.map((node: NodeType) => {
                return {
                    name: node.id,
                    value: node.id,
                    label: node.title,
                    dataType: node.value !== null && 'dataType' in node.value ? node.value.dataType : null,
                    valueType: node.value !== null ? node.value.value : null,
                };
            }),
        };
    }

    getOperators = field => {
        const { fields } = this.state;

        const fieldObject = fields.find(item => item.name === field);

        if (fieldObject === undefined || fieldObject.valueType === null) {
            return [];
        }

        return operators.filter(operator => operator.types.includes(fieldObject.valueType as string));
    };

    render() {
        const { prefixes, handleChange, value, save } = this.props;
        const { fields } = this.state;
        const ruleGroup = value as RuleGroupType;

        return (
            <>
                <div className="DependencyEditor">
                    <QueryBuilder
                        query={ruleGroup}
                        fields={fields}
                        showCombinatorsBetweenRules={true}
                        onQueryChange={handleChange}
                        controlElements={{
                            addGroupAction: props => (
                                <Button startIcon={<AddIcon />} onClick={props.handleOnClick}>
                                    Group
                                </Button>
                            ),
                            removeGroupAction: props => (
                                <Button startIcon={<DeleteIcon />} color="error" onClick={props.handleOnClick} />
                            ),
                            addRuleAction: props => (
                                <Button startIcon={<AddIcon />} onClick={props.handleOnClick}>
                                    Condition
                                </Button>
                            ),
                            removeRuleAction: props => (
                                <Button startIcon={<DeleteIcon />} color="error" onClick={props.handleOnClick} />
                            ),
                            combinatorSelector: props => {
                                return (
                                    <CombinatorSelector
                                        options={props.options.map(option => {
                                            return { value: option.name, label: option.label, name: option.name };
                                        })}
                                        value={props.value}
                                        handleOnChange={props.handleOnChange}
                                    />
                                );
                            },
                            fieldSelector: props => {
                                return (
                                    <FieldSelector
                                        options={props.options.map(option => {
                                            return { value: option.name, label: option.label, name: option.name };
                                        })}
                                        value={props.value}
                                        handleOnChange={props.handleOnChange}
                                    />
                                );
                            },
                            operatorSelector: props => {
                                return (
                                    <OperatorSelector
                                        options={props.options.map(option => {
                                            return { value: option.name, label: option.label, name: option.name };
                                        })}
                                        value={props.value}
                                        handleOnChange={props.handleOnChange}
                                    />
                                );
                            },
                            ruleGroup: props => <RuleGroup {...props} />,
                            rule: props => <Rule prefixes={prefixes} institutes={[]} {...props} />,
                            valueEditor: props => <ValueEditor prefixes={prefixes} institutes={[]} {...props} />,
                        }}
                        operators={operators}
                        getOperators={this.getOperators}
                    />
                </div>

                <footer>
                    <Button onClick={() => save(true)}>Edit dependencies</Button>
                </footer>
            </>
        );
    }
}

const operators = [
    { name: '=', label: '=', types: ['plain', 'annotated'] },
    { name: '!=', label: '!=', types: ['plain', 'annotated'] },
    { name: '<', label: '<', types: ['plain'] },
    { name: '>', label: '>', types: ['plain'] },
    { name: '<=', label: '<=', types: ['plain'] },
    { name: '>=', label: '>=', types: ['plain'] },
    { name: 'null', label: 'is empty', types: ['plain', 'annotated'] },
    { name: 'notNull', label: 'is not empty', types: ['plain', 'annotated'] },
];

export default ModuleDependencyEditor;
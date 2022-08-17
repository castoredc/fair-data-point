import React, { Component } from 'react';
import Dropdown from '../../Input/Dropdown';
import { Button } from '@castoredc/matter';
import '../DependencyEditor.scss';
import QueryBuilder from 'react-querybuilder';
import { RuleGroup } from './RuleGroup';
import { Rule } from './Rule';
import { ValueEditor } from './ValueEditor';
import { ValidatorForm } from 'react-form-validator-core';

export default class DistributionContentsDependencyEditor extends Component {
    constructor(props) {
        super(props);

        let fields = [
            {
                label: 'Record details',
                options: [
                    {
                        type: 'recordDetails',
                        value: 'institute',
                        name: 'institute',
                        label: 'Institute',
                        valueType: 'institute',
                    },
                ],
            },
        ];

        // if (props.type === 'rdf') {
        //     const valueNodes = props.valueNodes.map((node) => {
        //         return {
        //             type: 'valueNode',
        //             value: node.id,
        //             name: node.id,
        //             label: node.title,
        //             dataType: node.value.dataType,
        //             valueType: node.value.value,
        //         }
        //     });
        //
        //     fields = fields.concat({
        //         label: 'Value nodes',
        //         options: valueNodes
        //     });
        // }

        this.state = {
            fields: fields,
        };
    }

    getOperators = field => {
        if (typeof field === 'undefined') {
            return operators;
        }

        return operators.filter(operator => operator.types.includes(field.valueType));
    };

    handleSubmit = () => {
        const { save } = this.props;

        if (this.form.isFormValid()) {
            save();
        }
    };

    render() {
        const { prefixes, institutes, handleChange, value } = this.props;
        const { fields } = this.state;

        return (
            <ValidatorForm ref={node => (this.form = node)} onSubmit={this.handleSubmit} method="post">
                <div className="DependencyEditor">
                    <QueryBuilder
                        query={value}
                        fields={fields}
                        showCombinatorsBetweenRules={true}
                        onQueryChange={handleChange}
                        controlElements={{
                            addGroupAction: props => (
                                <Button icon="add" buttonType="primary" onClick={props.handleOnClick}>
                                    Group
                                </Button>
                            ),
                            removeGroupAction: props => (
                                <Button icon="trash" buttonType="danger" onClick={props.handleOnClick} iconDescription="Delete group" />
                            ),
                            addRuleAction: props => (
                                <Button icon="add" buttonType="primary" onClick={props.handleOnClick}>
                                    Condition
                                </Button>
                            ),
                            removeRuleAction: props => (
                                <Button icon="trash" buttonType="danger" onClick={props.handleOnClick} iconDescription="Delete condition" />
                            ),
                            combinatorSelector: props => (
                                <Dropdown
                                    value={props.value}
                                    onChange={e => props.handleOnChange(e.value)}
                                    menuPosition="fixed"
                                    width="minimum"
                                    options={props.options.map(option => {
                                        return { value: option.name, label: option.label };
                                    })}
                                />
                            ),
                            fieldSelector: props => (
                                <Dropdown
                                    value={props.value}
                                    onChange={e => {
                                        props.handleOnChange(e);
                                    }}
                                    menuPosition="fixed"
                                    width="tiny"
                                    isGrouped
                                    options={props.options}
                                />
                            ),
                            operatorSelector: props => (
                                <Dropdown
                                    value={props.value}
                                    onChange={e => props.handleOnChange(e.value)}
                                    menuPosition="fixed"
                                    width="minimum"
                                    options={props.options.map(option => {
                                        return { value: option.name, label: option.label };
                                    })}
                                />
                            ),
                            ruleGroup: props => <RuleGroup {...props} />,
                            rule: props => <Rule {...props} />,
                            valueEditor: props => <ValueEditor prefixes={prefixes} institutes={institutes} {...props} />,
                        }}
                        operators={operators}
                        getOperators={this.getOperators}
                    />
                </div>

                <br />

                <Button type="submit">Save dependencies</Button>
            </ValidatorForm>
        );
    }
}

const operators = [
    { name: '=', label: '=', types: ['plain', 'annotated', 'institute'] },
    { name: '!=', label: '!=', types: ['plain', 'annotated', 'institute'] },
    { name: '<', label: '<', types: ['plain'] },
    { name: '>', label: '>', types: ['plain'] },
    { name: '<=', label: '<=', types: ['plain'] },
    { name: '>=', label: '>=', types: ['plain'] },
    { name: 'null', label: 'is empty', types: ['plain', 'annotated'] },
    { name: 'notNull', label: 'is not empty', types: ['plain', 'annotated'] },
];

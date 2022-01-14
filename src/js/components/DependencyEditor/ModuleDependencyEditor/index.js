import React, {Component} from "react";
import Dropdown from "../../Input/Dropdown";
import {Button} from "@castoredc/matter";
import '../DependencyEditor.scss';
import QueryBuilder from 'react-querybuilder';
import {RuleGroup} from "./RuleGroup";
import {Rule} from "./Rule";
import {ValueEditor} from "./ValueEditor";
import {ValidatorForm} from "react-form-validator-core";

export default class ModuleDependencyEditor extends Component {
    constructor(props) {
        super(props);

        this.state = {
            fields: props.valueNodes.map((node) => {
                return {
                    name: node.id,
                    label: node.title,
                    dataType: node.value.dataType,
                    valueType: node.value.value,
                }
            }),
        }
    }

    getOperators = (field) => {
        const {fields} = this.state;

        const fieldObject = fields.find((item) => item.name === field);
        return operators.filter((operator) => operator.types.includes(fieldObject.valueType));
    };

    handleSubmit = () => {
        const {save} = this.props;

        if (this.form.isFormValid()) {
            save(true);
        }
    };

    render() {
        const {prefixes, handleChange, value} = this.props;
        const {fields} = this.state;

        return <ValidatorForm
            ref={node => (this.form = node)}
            onSubmit={this.handleSubmit}
            method="post"
        >
            <div className="DependencyEditor">
                <QueryBuilder
                    query={value}
                    fields={fields}
                    showCombinatorsBetweenRules={true}
                    onQueryChange={handleChange}
                    controlElements={{
                        addGroupAction: (props) => <Button icon="add" buttonType="primary"
                                                           onClick={props.handleOnClick}>Group</Button>,
                        removeGroupAction: (props) => <Button icon="trash" buttonType="danger"
                                                              onClick={props.handleOnClick}
                                                              iconDescription="Delete group"/>,
                        addRuleAction: (props) => <Button icon="add" buttonType="primary"
                                                          onClick={props.handleOnClick}>Condition</Button>,
                        removeRuleAction: (props) => <Button icon="trash" buttonType="danger"
                                                             onClick={props.handleOnClick}
                                                             iconDescription="Delete condition"/>,
                        combinatorSelector: (props) => <Dropdown value={props.value}
                                                                 onChange={(e) => props.handleOnChange(e.value)}
                                                                 menuPosition="fixed"
                                                                 width="minimum"
                                                                 options={props.options.map((option) => {
                                                                     return {value: option.name, label: option.label};
                                                                 })}
                        />,
                        fieldSelector: (props) => <Dropdown value={props.value}
                                                            onChange={(e) => props.handleOnChange(e.value)}
                                                            menuPosition="fixed"
                                                            width="tiny" options={props.options.map((option) => {
                            return {value: option.name, label: option.label};
                        })}
                        />,
                        operatorSelector: (props) => <Dropdown value={props.value}
                                                               onChange={(e) => props.handleOnChange(e.value)}
                                                               menuPosition="fixed"
                                                               width="minimum"
                                                               options={props.options.map((option) => {
                                                                   return {value: option.name, label: option.label};
                                                               })}
                        />,
                        ruleGroup: (props) => <RuleGroup {...props} />,
                        rule: (props) => <Rule {...props} />,
                        valueEditor: (props) => <ValueEditor prefixes={prefixes} {...props} />,
                    }}
                    operators={operators}
                    getOperators={this.getOperators}
                />
            </div>

            <footer>
                <Button type="submit">
                    Edit dependencies
                </Button>
            </footer>

        </ValidatorForm>;
    }
}

const operators = [
    {name: '=', label: '=', types: ['plain', 'annotated']},
    {name: '!=', label: '!=', types: ['plain', 'annotated']},
    {name: '<', label: '<', types: ['plain']},
    {name: '>', label: '>', types: ['plain']},
    {name: '<=', label: '<=', types: ['plain']},
    {name: '>=', label: '>=', types: ['plain']},
    {name: 'null', label: 'is empty', types: ['plain', 'annotated']},
    {name: 'notNull', label: 'is not empty', types: ['plain', 'annotated']},
];
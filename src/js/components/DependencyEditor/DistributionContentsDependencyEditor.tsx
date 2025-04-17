import React, { FC } from 'react';
import { Form, Formik } from 'formik';
import Button from '@mui/material/Button';
import AddIcon from '@mui/icons-material/Add';
import DeleteIcon from '@mui/icons-material/Delete';
import QueryBuilder, { Field } from 'react-querybuilder';
import { Rule } from '../QueryBuilder/Rule';
import { ValueEditor } from '../QueryBuilder/ValueEditor';
import './DependencyEditor.scss';
import { findOptionByValue } from '../../util';
import CombinatorSelector from 'components/QueryBuilder/CombinatorSelector';
import FieldSelector from 'components/QueryBuilder/FieldSelector';
import OperatorSelector from 'components/QueryBuilder/OperatorSelector';
import { PrefixType } from 'types/PrefixType';
import { InstituteType } from 'types/InstituteType';
import { RuleGroup } from 'components/QueryBuilder/RuleGroup';
import { NodeType } from 'types/NodeType';

interface Operator {
    name: string;
    label: string;
    types: string[];
}

interface DistributionContentsDependencyEditorProps {
    prefixes: PrefixType[];
    institutes: InstituteType[];
    handleChange: (query: any) => void;
    save: () => void;
    value: any;
    type: 'rdf' | 'csv';
    valueNodes?: NodeType[];
}

const DistributionContentsDependencyEditor: FC<DistributionContentsDependencyEditorProps> = ({
                                                                                                 prefixes,
                                                                                                 institutes,
                                                                                                 handleChange,
                                                                                                 save,
                                                                                                 value,
                                                                                                 type,
                                                                                                 valueNodes,
                                                                                             }) => {
    let fields: Field[] = [
        {
            label: 'Record details',
            name: 'recordDetails',
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

    if (type === 'rdf' && valueNodes) {
        fields = fields.concat({
            label: 'Value nodes',
            name: 'valueNodes',
            options: valueNodes.map(node => {
                return {
                    type: 'valueNode',
                    name: node.id,
                    value: node.id,
                    label: node.title,
                    dataType: node.value !== null && 'dataType' in node.value ? node.value.dataType : null,
                    valueType: node.value !== null ? node.value.value : null,
                };
            }),
        });
    }

    const getOperators = (fieldName: string): Operator[] => {
        const field = findOptionByValue(fieldName, fields);

        if (!field) {
            return operators;
        }
        return operators.filter(operator => operator.types.includes(field.valueType));
    };

    return (
        <Formik
            initialValues={{ query: value }}
            onSubmit={(values, { setSubmitting }) => {
                save();
                setSubmitting(false);
            }}
        >
            {({ values, setFieldValue, isValid, isSubmitting }) => (
                <Form method="post">
                    <div className="DependencyEditor" style={{ width: '700px' }}>
                        <QueryBuilder
                            query={values.query}
                            fields={fields}
                            showCombinatorsBetweenRules
                            onQueryChange={newQuery => {
                                setFieldValue('query', newQuery);
                                handleChange(newQuery);
                            }}
                            controlElements={{
                                addGroupAction: props => (
                                    <Button startIcon={<AddIcon />} variant="contained" onClick={props.handleOnClick}>
                                        Group
                                    </Button>
                                ),
                                removeGroupAction: props => (
                                    <Button startIcon={<DeleteIcon />} variant="contained" color="error"
                                            onClick={props.handleOnClick} />
                                ),
                                addRuleAction: props => (
                                    <Button startIcon={<AddIcon />} variant="contained" onClick={props.handleOnClick}>
                                        Condition
                                    </Button>
                                ),
                                removeRuleAction: props => (
                                    <Button startIcon={<DeleteIcon />} variant="contained" color="error"
                                            onClick={props.handleOnClick} />
                                ),
                                combinatorSelector: props => {
                                    return <CombinatorSelector options={props.options} value={props.value}
                                                               handleOnChange={props.handleOnChange} />;
                                },
                                fieldSelector: props => {
                                    return <FieldSelector options={props.options} value={props.value}
                                                          handleOnChange={props.handleOnChange} />;
                                },
                                operatorSelector: props => {
                                    return <OperatorSelector options={props.options} value={props.value}
                                                             handleOnChange={props.handleOnChange} />;
                                },
                                ruleGroup: props => <RuleGroup {...props} />,
                                rule: props => <Rule prefixes={prefixes} institutes={institutes} {...props} />,
                                valueEditor: props => <ValueEditor prefixes={prefixes}
                                                                   institutes={institutes} {...props} />,
                            }}
                            operators={operators}
                            getOperators={getOperators}
                        />
                    </div>

                    <br />

                    <Button type="submit" variant="contained" disabled={isSubmitting}>
                        Save dependencies
                    </Button>
                </Form>
            )}
        </Formik>
    );
};

// Define operators and fields as constants for use within the component
const operators: Operator[] = [
    { name: '=', label: '=', types: ['plain', 'annotated', 'institute'] },
    { name: '!=', label: '!=', types: ['plain', 'annotated', 'institute'] },
    { name: '<', label: '<', types: ['plain'] },
    { name: '>', label: '>', types: ['plain'] },
    { name: '<=', label: '<=', types: ['plain'] },
    { name: '>=', label: '>=', types: ['plain'] },
    { name: 'null', label: 'is empty', types: ['plain', 'annotated'] },
    { name: 'notNull', label: 'is not empty', types: ['plain', 'annotated'] },
];

export default DistributionContentsDependencyEditor;

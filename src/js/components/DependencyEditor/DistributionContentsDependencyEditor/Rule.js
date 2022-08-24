import { Stack } from '@castoredc/matter';
import React from 'react';

export const Rule = ({ id, parentId, field, operator, value, translations, schema }) => {
    const { classNames, controls, fields, getInputType, getLevel, getOperators, getValueEditorType, getValues, onPropChange, onRuleRemove } = schema;

    const onElementChanged = (property, value) => {
        onPropChange(property, value, id);
    };

    const onFieldChanged = value => {
        onElementChanged('field', value);
    };

    const onOperatorChanged = value => {
        onElementChanged('operator', value);
    };

    const onValueChanged = value => {
        onElementChanged('value', value);
    };

    const removeRule = event => {
        event.preventDefault();
        event.stopPropagation();

        onRuleRemove(id, parentId);
    };

    const level = getLevel(id);

    return (
        <div className={`rule`} data-rule-id={id} data-level={level}>
            <Stack>
                <controls.fieldSelector
                    options={fields}
                    title={translations.fields.title}
                    value={field}
                    operator={operator}
                    className={`rule-fields`}
                    handleOnChange={onFieldChanged}
                    level={level}
                />
                <controls.operatorSelector
                    field={field}
                    title={translations.operators.title}
                    options={getOperators(field)}
                    value={operator}
                    className={`rule-operators`}
                    handleOnChange={onOperatorChanged}
                    level={level}
                />
                <controls.valueEditor
                    field={field}
                    title={translations.value.title}
                    operator={operator}
                    value={value}
                    type={getValueEditorType(field, operator)}
                    inputType={getInputType(field, operator)}
                    values={getValues(field, operator)}
                    className={`rule-value`}
                    handleOnChange={onValueChanged}
                    level={level}
                    key={id + '_editor'}
                />
                <controls.removeRuleAction
                    label={translations.removeRule.label}
                    title={translations.removeRule.title}
                    className={`rule-remove`}
                    handleOnClick={removeRule}
                    level={level}
                />
            </Stack>
        </div>
    );
};

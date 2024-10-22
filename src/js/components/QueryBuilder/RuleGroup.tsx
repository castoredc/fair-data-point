// @ts-nocheck
import { Stack } from '@castoredc/matter';
import React, { FC } from 'react';
import { RuleGroupProps as QueryBuilderRuleGroupProps } from 'react-querybuilder/types/types';

interface RuleGroupProps extends QueryBuilderRuleGroupProps {}

export const RuleGroup: FC<RuleGroupProps> = ({ id, parentId, combinator, rules = [], translations, schema, not }) => {
    const {
        combinators,
        controls,
        createRule,
        createRuleGroup,
        getLevel,
        isRuleGroup,
        onGroupAdd,
        onGroupRemove,
        onPropChange,
        onRuleAdd,
        showCombinatorsBetweenRules,
        showNotToggle,
    } = schema;

    const hasParentGroup = () => !!parentId;

    const onCombinatorChange = value => {
        onPropChange('combinator', value, id);
    };

    const onNotToggleChange = checked => {
        onPropChange('not', checked, id);
    };

    const addRule = event => {
        event.preventDefault();
        event.stopPropagation();

        const newRule = createRule();
        onRuleAdd(newRule, id);
    };

    const addGroup = event => {
        event.preventDefault();
        event.stopPropagation();

        const newGroup = createRuleGroup();
        onGroupAdd(newGroup, id);
    };

    const removeGroup = event => {
        event.preventDefault();
        event.stopPropagation();

        onGroupRemove(id, parentId || '');
    };

    const level = getLevel(id);

    const length = rules.length - 1;

    if (combinator === undefined) {
        combinator = 'and';
    }

    return (
        <div className={`RuleGroup`} data-rule-group-id={id} data-level={level}>
            <div className={`RuleGroup-header`}>
                <Stack alignment="normal" distribution="equalSpacing">
                    <Stack>
                        {showCombinatorsBetweenRules ? null : (
                            <div className="RuleGroupCombinator">
                                <controls.combinatorSelector
                                    options={combinators}
                                    value={combinator}
                                    title={translations.combinators.title}
                                    className={`RuleGroup-combinators`}
                                    handleOnChange={onCombinatorChange}
                                    rules={rules}
                                    level={level}
                                />
                            </div>
                        )}
                        {!showNotToggle ? null : (
                            <controls.notToggle
                                className={`RuleGroup-notToggle`}
                                title={translations.notToggle.title}
                                checked={not}
                                handleOnChange={onNotToggleChange}
                                level={level}
                            />
                        )}
                    </Stack>
                    <Stack>
                        <controls.addRuleAction
                            label={translations.addRule.label}
                            title={translations.addRule.title}
                            className={`RuleGroup-addRule`}
                            handleOnClick={addRule}
                            rules={rules}
                            level={level}
                        />
                        <controls.addGroupAction
                            label={translations.addGroup.label}
                            title={translations.addGroup.title}
                            className={`RuleGroup-addGroup`}
                            handleOnClick={addGroup}
                            rules={rules}
                            level={level}
                        />
                        {hasParentGroup() ? (
                            <controls.removeGroupAction
                                label={translations.removeGroup.label}
                                title={translations.removeGroup.title}
                                className={`RuleGroup-remove`}
                                handleOnClick={removeGroup}
                                rules={rules}
                                level={level}
                            />
                        ) : null}
                    </Stack>
                </Stack>
            </div>
            {rules.map((r, idx) => (
                <div className="RuleGroupRules" key={r.id}>
                    {idx && showCombinatorsBetweenRules ? (
                        <div className="RuleGroupCombinator">
                            <controls.combinatorSelector
                                options={combinators}
                                value={combinator}
                                title={translations.combinators.title}
                                className={`RuleGroup-combinators betweenRules`}
                                handleOnChange={onCombinatorChange}
                                rules={rules}
                                level={level}
                            />
                        </div>
                    ) : null}
                    {isRuleGroup(r) ? (
                        <RuleGroup
                            id={r.id}
                            schema={schema}
                            parentId={id}
                            combinator={r.combinator}
                            translations={translations}
                            rules={r.rules}
                            not={!!r.not}
                        />
                    ) : (
                        <controls.rule
                            id={r.id}
                            field={r.field}
                            value={r.value}
                            operator={r.operator}
                            schema={schema}
                            parentId={id}
                            translations={translations}
                        />
                    )}
                </div>
            ))}
        </div>
    );
};

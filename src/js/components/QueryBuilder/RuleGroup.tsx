// @ts-nocheck

import React, { FC } from 'react';
import { RuleGroupProps as QueryBuilderRuleGroupProps } from 'react-querybuilder/types/types';
import Stack from '@mui/material/Stack';
import Card from '@mui/material/Card';
import CardHeader from '@mui/material/CardHeader';
import CardContent from '@mui/material/CardContent';
import Box from '@mui/material/Box';

interface RuleGroupProps extends QueryBuilderRuleGroupProps {
}

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

    const onCombinatorChange = event => {
        onPropChange('combinator', event.target.value, id);
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
    const finalCombinator = combinator || 'and';

    return (
        <Card
            variant="outlined"
            sx={{
                mb: 2,
            }}
            data-rule-group-id={id}
            data-level={level}
        >
            <CardHeader
                sx={{ p: 2 }}
                action={
                    <Stack direction="row" spacing={1}>
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
                        {hasParentGroup() && (
                            <controls.removeGroupAction
                                label={translations.removeGroup.label}
                                title={translations.removeGroup.title}
                                className={`RuleGroup-remove`}
                                handleOnClick={removeGroup}
                                rules={rules}
                                level={level}
                            />
                        )}
                    </Stack>
                }
                title={
                    <Stack direction="row" spacing={2} alignItems="center">
                        {!showCombinatorsBetweenRules && (
                            <Box className="RuleGroupCombinator">
                                <controls.combinatorSelector
                                    options={combinators}
                                    value={finalCombinator}
                                    title={translations.combinators.title}
                                    className={`RuleGroup-combinators`}
                                    handleOnChange={onCombinatorChange}
                                    rules={rules}
                                    level={level}
                                />
                            </Box>
                        )}
                        {showNotToggle && (
                            <controls.notToggle
                                className={`RuleGroup-notToggle`}
                                title={translations.notToggle.title}
                                checked={not}
                                handleOnChange={onNotToggleChange}
                                level={level}
                            />
                        )}
                    </Stack>
                }
            />
            <CardContent sx={{ p: 2 }}>
                <Stack spacing={2}>
                    {rules.map((r, idx) => (
                        <Box key={r.id}>
                            {showCombinatorsBetweenRules && (
                                <Box className="RuleGroupCombinator" sx={{ mb: 2 }}>
                                    <controls.combinatorSelector
                                        options={combinators}
                                        value={finalCombinator}
                                        title={translations.combinators.title}
                                        className={`RuleGroup-combinators betweenRules`}
                                        handleOnChange={onCombinatorChange}
                                        rules={rules}
                                        level={level}
                                    />
                                </Box>
                            )}
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
                        </Box>
                    ))}
                </Stack>
            </CardContent>
        </Card>
    );
};

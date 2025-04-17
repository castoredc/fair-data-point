import React from 'react';
import FieldSelector from 'components/QueryBuilder/FieldSelector';
import OperatorSelector from 'components/QueryBuilder/OperatorSelector';
import { RuleProps as QueryBuilderRuleProps } from 'react-querybuilder/types/types';
import { ValueEditor } from 'components/QueryBuilder/ValueEditor';
import { PrefixType } from 'types/PrefixType';
import { InstituteType } from 'types/InstituteType';
import { findOptionByValue } from '../../util';
import Stack from '@mui/material/Stack';
import { IconButton, TextField } from '@mui/material';
import DeleteIcon from '@mui/icons-material/Delete';

interface RuleProps extends QueryBuilderRuleProps {
    prefixes: PrefixType[];
    institutes: InstituteType[];
}

export const Rule: React.FC<RuleProps> = ({
                                              id,
                                              parentId,
                                              field,
                                              operator,
                                              value,
                                              translations,
                                              schema,
                                              prefixes,
                                              institutes,
                                          }) => {
    const { fields, getLevel, getOperators, onPropChange, onRuleRemove } = schema;

    const level = getLevel(id);
    const fieldData = findOptionByValue(field, fields);

    return (
        <div className={`rule`} data-rule-id={id} data-level={level}>
            <Stack direction="row">
                <FieldSelector
                    value={field}
                    options={fields}
                    handleOnChange={(value: string) => {
                        onPropChange('field', value, id);
                    }}
                />

                <OperatorSelector
                    value={operator}
                    options={getOperators(field)}
                    handleOnChange={(value: string) => {
                        onPropChange('operator', value, id);
                    }}
                />

                {fieldData ? (
                    <ValueEditor
                        prefixes={prefixes}
                        institutes={institutes}
                        field={field}
                        fieldData={fieldData}
                        operator={operator}
                        handleOnChange={(value: any) => {
                            onPropChange('value', value, id);
                        }}
                        level={level}
                        value={value}
                    />
                ) : (
                    <TextField className="ValueEditor" disabled />
                )}

                <IconButton
                    className="RemoveButton"
                    color="error"
                    onClick={() => onRuleRemove(id, parentId)}
                >
                    <DeleteIcon />
                </IconButton>
            </Stack>
        </div>
    );
};

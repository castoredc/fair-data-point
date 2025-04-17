import React, { FC } from 'react';

import FormItem from '../FormItem';
import Button from '@mui/material/Button';

import { AffiliationType } from 'types/AffiliationType';
import CustomIcon from 'components/Icon/CustomIcon';
import Avatar from 'react-avatar';
import { Field } from 'formik';
import Select from 'components/Input/Formik/Select';
import OrganizationSelect from 'components/Input/Formik/OrganizationSelect';
import DepartmentSelect from 'components/Input/Formik/DepartmentSelect';
import Input from 'components/Input/Formik/Input';
import ClearIcon from '@mui/icons-material/Clear';
import Stack from '@mui/material/Stack';
import CorporateFareIcon from '@mui/icons-material/CorporateFare';
import { Avatar as MuiAvatar, Card, CardContent } from '@mui/material';

interface AffiliationFormProps {
    values: AffiliationType;
    validation: any;
    countries: any;
    index: number;
    name: string;
    setFieldValue: (field: string, value: any, shouldValidate?: boolean) => void;
    handleRemove: () => void;
}

const AffiliationForm: FC<AffiliationFormProps> = ({
                                                       values,
                                                       validation,
                                                       countries,
                                                       index,
                                                       name,
                                                       handleRemove,
                                                       setFieldValue,
                                                   }) => {
    const prefix = `${name}.${index}`;

    // @ts-ignore
    return (
        <Card className="AffiliationForm" variant="outlined" sx={{ p: 1, mb: 2 }}>
            <CardContent>
                <Stack direction="row" spacing={2}>
                    <div className="AffiliationAvatar">
                        {values.country != '' && values.organization.name != '' ? (
                            /* @ts-ignore */
                            <Avatar name={values.organization.name} size="48px" round />
                        ) : (
                            <div className="BlankAvatar">
                                <MuiAvatar
                                    sx={{ width: 48, height: 48 }}
                                >
                                    <CorporateFareIcon />
                                </MuiAvatar>
                            </div>
                        )}
                    </div>
                    <div>
                        <Stack spacing={3}>
                            <Stack direction="row" spacing={2}>
                                <FormItem label="Country">
                                    <Field
                                        component={Select}
                                        options={countries}
                                        name={`${prefix}.country`}

                                        serverError={validation}
                                        onChange={() => {
                                            setFieldValue(`${prefix}.organization`, {
                                                id: null,
                                                name: '',
                                                source: '',
                                                city: '',
                                            });
                                        }}
                                    />
                                </FormItem>

                                <Field
                                    component={OrganizationSelect}
                                    label="Organization"
                                    name={`${prefix}.organization`}
                                    departmentField={`${prefix}.department`}
                                    country={values.country}
                                />
                            </Stack>

                            <Stack direction="row" spacing={2}>
                                <Field component={DepartmentSelect} label="Department" name={`${prefix}.department`}
                                       organization={values.organization} />

                                <FormItem label="Position">
                                    <Field component={Input} name={`${prefix}.position`}
                                           readOnly={values.department.source === ''} />
                                </FormItem>
                            </Stack>
                        </Stack>
                    </div>
                </Stack>
                {index !== 0 && (
                    <Stack direction="row" sx={{ mt: 2, justifyContent: 'flex-end' }}>
                        <Button startIcon={<ClearIcon />} className="RemoveButton" onClick={handleRemove} color="error">
                            Remove affiliation
                        </Button>
                    </Stack>
                )}
            </CardContent>
        </Card>
    );
};

export default AffiliationForm;

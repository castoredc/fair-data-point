import React, { FC } from 'react';

import '../Form.scss';
import FormItem from '../FormItem';
import { Button, Stack } from '@castoredc/matter';
import { AffiliationType } from 'types/AffiliationType';
import Avatar from 'react-avatar';
import CustomIcon from 'components/Icon/CustomIcon';
import { Field } from 'formik';
import Select from 'components/Input/Formik/Select';
import OrganizationSelect from 'components/Input/Formik/OrganizationSelect';
import DepartmentSelect from 'components/Input/Formik/DepartmentSelect';
import Input from 'components/Input/Formik/Input';

interface AffiliationFormProps {
    values: AffiliationType;
    validation: any;
    countries: any;
    index: number;
    name: string;
    setFieldValue: (field: string, value: any, shouldValidate?: boolean) => void;
    handleRemove: () => void;
}

const AffiliationForm: FC<AffiliationFormProps> = ({ values, validation, countries, index, name, handleRemove, setFieldValue }) => {
    const prefix = `${name}.${index}`;

    // @ts-ignore
    return (
        <div className="AffiliationForm">
            <Stack>
                <div className="AffiliationAvatar">
                    {values.country != '' && values.organization.name != '' ? (
                        /* @ts-ignore */
                        <Avatar name={values.organization.name} size="48px" round />
                    ) : (
                        <div className="BlankAvatar">
                            <CustomIcon type="center" width={28} height={28} />
                        </div>
                    )}
                </div>
                <div>
                    <Stack>
                        <FormItem label="Country">
                            <Field
                                component={Select}
                                options={countries}
                                name={`${prefix}.country`}
                                menuPosition="fixed"
                                serverError={validation}
                                onChange={(value, action) => {
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

                    <Stack>
                        <Field component={DepartmentSelect} label="Department" name={`${prefix}.department`} organization={values.organization} />

                        <FormItem label="Position">
                            <Field component={Input} name={`${prefix}.position`} readOnly={values.department.source === ''} />
                        </FormItem>
                    </Stack>
                </div>
            </Stack>
            {index !== 0 && (
                <div className="AffiliationButtons">
                    <Stack distribution="trailing">
                        <Button icon="cross" className="RemoveButton" onClick={handleRemove} buttonType="danger" iconDescription="Remove">
                            Remove affiliation
                        </Button>
                    </Stack>
                </div>
            )}
        </div>
    );
};

export default AffiliationForm;

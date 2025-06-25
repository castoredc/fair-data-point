import React, { FC } from 'react';
import { RenderedMetadataFormType } from 'types/RenderedMetadataFormType';
import RenderedFormField from 'components/Form/Metadata/RenderedForm/RenderedFormField';
import { DataSpecificationOptionGroupType } from 'types/DataSpecificationOptionGroupType';
import { LanguageType } from 'types/LanguageType';
import { LicenseType } from 'types/LicenseType';
import { CountryType } from 'types/CountryType';
import { Card, CardContent, Typography } from '@mui/material';

type RenderedFormProps = {
    form: RenderedMetadataFormType;
    validation: any;
    optionGroups: DataSpecificationOptionGroupType[];
    languages: LanguageType[];
    licenses: LicenseType[];
    countries: CountryType[];
};

const RenderedForm: FC<RenderedFormProps> = ({ form, validation, optionGroups, languages, licenses, countries }) => {
    return (
        <Card variant="outlined" sx={{ mb: 2 }}>
            <CardContent>
                <Typography variant="h5" component="div">
                    {form.title}
                </Typography>

                <div>
                    {form.fields.map(field => {
                        return (
                            <RenderedFormField
                                key={field.id}
                                field={field}
                                validation={validation}
                                optionGroups={optionGroups}
                                languages={languages}
                                licenses={licenses}
                                countries={countries}
                            />
                        );
                    })}
                </div>
            </CardContent>
        </Card>
    );
};

export default RenderedForm;

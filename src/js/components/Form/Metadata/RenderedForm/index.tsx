import React, { FC } from 'react';
import { RenderedMetadataFormType } from 'types/RenderedMetadataFormType';
import { Card } from '@castoredc/matter';
import RenderedFormField from 'components/Form/Metadata/RenderedForm/RenderedFormField';
import { DataSpecificationOptionGroupType } from 'types/DataSpecificationOptionGroupType';
import { LanguageType } from 'types/LanguageType';
import { LicenseType } from 'types/LicenseType';
import { CountryType } from 'types/CountryType';

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
        <Card title={form.title}>
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
        </Card>
    );
};

export default RenderedForm;

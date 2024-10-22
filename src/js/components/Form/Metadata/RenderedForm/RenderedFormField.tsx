import React, { FC } from 'react';
import { RenderedMetadataFormFieldType } from 'types/RenderedMetadataFormType';
import { Field } from 'formik';
import Input from 'components/Input/Formik/Input';
import LocalizedTextInput from 'components/Input/Formik/LocalizedTextInput';
import OntologyConceptFormBlock from 'components/Input/Formik/OntologyConceptFormBlock';
import DatePicker from 'components/Input/Formik/DatePicker';
import TimePicker from 'components/Input/Formik/TimePicker';
import SingleChoice from 'components/Input/Formik/SingleChoice';
import Choice from 'components/Input/Formik/Choice';
import Select from 'components/Input/Formik/Select';
import { DataSpecificationOptionGroupType } from 'types/DataSpecificationOptionGroupType';
import { LanguageType } from 'types/LanguageType';
import { LicenseType } from 'types/LicenseType';
import { CountryType } from 'types/CountryType';
import FormItem from 'components/Form/FormItem';
import AgentPicker from 'components/Input/Formik/AgentPicker';
import DateAndTimePicker from 'components/Input/Formik/DateAndTimePicker';

type RenderedFormFieldProps = {
    field: RenderedMetadataFormFieldType;
    validation: any;
    optionGroups: DataSpecificationOptionGroupType[];
    languages: LanguageType[];
    licenses: LicenseType[];
    countries: CountryType[];
};

const FieldComponent: FC<RenderedFormFieldProps> = ({ field, validation, optionGroups, languages, licenses, countries }) => {
    let options: {
        value: string,
        label: string
        labelText: string
    }[] = [];

    if(field.optionGroup) {
        const optionGroup = optionGroups.find((optionGroup) => optionGroup.id === field.optionGroup);
        if(optionGroup) {
            options = optionGroup.options.map((option) => {
                return {
                    label: option.title,
                    labelText: option.title,
                    value: option.value,
                }
            })
        }
    }

    switch (field.fieldType) {
        case 'input':
            return <Field
                component={Input}
                name={field.id}
                serverError={validation}
                multiline={false}
            />;
        case 'inputLocale':
            return <Field
                component={LocalizedTextInput}
                name={field.id}
                languages={languages}
                serverError={validation}
                multiline={false}
            />;
        case 'textarea':
            return <Field
                component={Input}
                name={field.id}
                serverError={validation}
                multiline={true}
            />;
        case 'textareaLocale':
            return <Field
                component={LocalizedTextInput}
                name={field.id}
                languages={languages}
                serverError={validation}
                multiline={true}
            />;
        case 'ontologyConceptBrowser':
            return <Field
                component={OntologyConceptFormBlock}
                name={field.id}
                serverError={validation}
            />;
        case 'datePicker':
            return <Field
                component={DatePicker}
                name={field.id}
                serverError={validation}
            />;
        case 'timePicker':
            return <Field
                component={TimePicker}
                name={field.id}
                serverError={validation}
            />;
        case 'dateAndTimePicker':
            return <Field
                component={DateAndTimePicker}
                name={field.id}
                serverError={validation}
            />;
        case 'checkbox':
            return <Field
                component={SingleChoice}
                name={field.id}
                labelText={field.title}
                details={field.description}
                serverError={validation}
            />;
        case 'checkboxes':
            return <Field
                component={Choice}
                name={field.id}
                options={options}
                serverError={validation}
                multiple={true}
            />;
        case 'radioButtons':
            return <Field
                component={Choice}
                name={field.id}
                options={options}
                serverError={validation}
                multiple={false}
            />;
        case 'dropdown':
            return <Field
                component={Select}
                name={field.id}
                options={options}
                serverError={validation}
                menuPosition="fixed"
                menuPlacement="auto"
            />;
        case 'languagePicker':
            return <Field
                component={Select}
                name={field.id}
                options={languages}
                serverError={validation}
                menuPosition="fixed"
                menuPlacement="auto"
            />;
        case 'licensePicker':
            return <Field
                component={Select}
                name={field.id}
                options={licenses}
                serverError={validation}
                menuPosition="fixed"
                menuPlacement="auto"
            />;
        case 'countryPicker':
            return <Field
                component={Select}
                name={field.id}
                options={countries}
                serverError={validation}
                menuPosition="fixed"
                menuPlacement="auto"
            />;
        case 'agentSelector':
            return <Field
                component={AgentPicker}
                name={field.id}
                countries={countries}
                serverError={validation}
                menuPosition="fixed"
                menuPlacement="auto"
            />;
        default:
            return null;
    }
};

const RenderedFormField: FC<RenderedFormFieldProps> = ({ field, validation, optionGroups, languages, licenses, countries }) => {
    const fieldComponent = <FieldComponent
        field={field}
        validation={validation}
        optionGroups={optionGroups}
        languages={languages}
        licenses={licenses}
        countries={countries}
    />;

    return <FormItem
        label={field.title}
        details={field.description}
        isRequired={field.isRequired}
    >
        {fieldComponent}
    </FormItem>;
};

export default RenderedFormField;

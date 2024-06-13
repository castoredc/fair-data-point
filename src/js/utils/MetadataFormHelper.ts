import { RenderedMetadataFormType } from 'types/RenderedMetadataFormType';
import * as Yup from 'yup';

const LocaleFieldTypes = [
    'inputLocale',
    'textareaLocale',
];

const ArrayFieldTypes = [
    'ontologyConceptBrowser',
    'checkboxes',
    'agentSelector'
];

const LocaleFieldTypeMappings = {
    required: {
        inputLocale: Yup.array()
            .of(
                Yup.object().shape({
                    text: Yup.string().required('Please enter text'),
                    language: Yup.string().nullable().required('Please select a language'),
                })
            ).nullable(),
        textareaLocale: Yup.array()
            .of(
                Yup.object().shape({
                    text: Yup.string().required('Please enter text'),
                    language: Yup.string().nullable().required('Please select a language'),
                })
            ).nullable(),
    },
    notRequired: {
        inputLocale: Yup.array()
            .of(
                Yup.object().shape({
                    text: Yup.string(),
                    language: Yup.string().nullable(),
                })
            ).nullable(),
        textareaLocale: Yup.array()
            .of(
                Yup.object().shape({
                    text: Yup.string(),
                    language: Yup.string().nullable(),
                })
            ).nullable(),
    }
}

const YupFieldTypeMappings = {
    input: Yup.string(),
    textarea: Yup.string(),
    ontologyConceptBrowser: Yup.array()
        .of(
            Yup.object().shape({
                code: Yup.string().required('This concept cannot be added'),
                url: Yup.string().required('This concept cannot be added'),
                displayName: Yup.string().required('This concept cannot be added'),
            })
        )
        .nullable(),
    datePicker: Yup.string(),
    timePicker: Yup.string(),
    dateAndTimePicker: Yup.string(),
    checkbox: Yup.boolean(),
    checkboxes: Yup.array().of(Yup.string()),
    radioButtons: Yup.string(),
    dropdown: Yup.string(),
    languagePicker: Yup.string(),
    licensePicker: Yup.string(),
    countryPicker: Yup.string(),
    agentSelector: Yup.array()
        .of(
            Yup.object().shape({
                type: Yup.string().required('Please select a type'),
            })
        )
        .nullable(),
};

export const getFields = (forms: RenderedMetadataFormType[]) => {
    return forms.map((form) => form.fields).flat();
}

export const getInitialValues = (forms: RenderedMetadataFormType[]) => {
    return Object.fromEntries(
        getFields(forms).map((field) => {
            return [field.id, field.value];
        })
    );
}

export const getSchema = (forms: RenderedMetadataFormType[]) => {
    const fields = getFields(forms);

    const schema = Object.fromEntries(
        fields.map((field) => {
            let fieldSchema = YupFieldTypeMappings[field.fieldType];

            if(LocaleFieldTypes.includes(field.fieldType)) {
                if(field.isRequired) {
                    fieldSchema = LocaleFieldTypeMappings.required[field.fieldType];
                } else {
                    fieldSchema = LocaleFieldTypeMappings.notRequired[field.fieldType];
                }
            }
            else if(ArrayFieldTypes.includes(field.fieldType) && field.isRequired) {
                fieldSchema = fieldSchema.min(1, 'Please add at least one item');
            } else if(field.isRequired) {
                fieldSchema = fieldSchema.required('This is a required field');
            }

            return [field.id, fieldSchema];
        })
    );

    return Yup.object().shape(schema);
}
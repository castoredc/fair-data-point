import { RenderedMetadataFormType } from 'types/RenderedMetadataFormType';
import * as Yup from 'yup';

const ArrayFieldTypes = [
    'inputLocale',
    'textareaLocale',
    'ontologyConceptBrowser',
    'checkboxes',
    'agentSelector'
];

const YupFieldTypeMappings = {
    input: Yup.string(),
    inputLocale: Yup.array()
        .of(
            Yup.object().shape({
                text: Yup.string().required('Please enter text'),
                language: Yup.string().nullable().required('Please select a language'),
            })
        ).nullable(),
    textarea: Yup.string(),
    textareaLocale: Yup.array()
        .of(
            Yup.object().shape({
                text: Yup.string().required('Please enter text'),
                language: Yup.string().nullable().required('Please select a language'),
            })
        ).nullable(),
    ontologyConceptBrowser: Yup.array()
        .of(
            Yup.object().shape({
                code: Yup.string().required('This concept cannot be added'),
                url: Yup.string().required('This concept cannot be added'),
                displayName: Yup.string().required('This concept cannot be added'),
                ontology: Yup.string().required('Please select an ontology'),
            })
        )
        .nullable(),
    datePicker: Yup.string(),
    timePicker: Yup.string(),
    // dateAndTimePicker: 'Date and timepicker',
    checkbox: Yup.boolean(),
    checkboxes: Yup.array().of(Yup.string()),
    radioButtons: Yup.string(),
    dropdown: Yup.string(),
    languagePicker: Yup.string(),
    licensePicker: Yup.string(),
    countryPicker: Yup.string(),
    agentSelector: Yup.array().of(Yup.string()),
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

            if(field.isRequired) {
                if(ArrayFieldTypes.includes(field.fieldType)) {
                    fieldSchema = fieldSchema.min(1, 'Please add at least one item');
                }

                fieldSchema = fieldSchema.required('This is a required field');
            }

            return [field.id, fieldSchema];
        })
    );

    return Yup.object().shape(schema);
}
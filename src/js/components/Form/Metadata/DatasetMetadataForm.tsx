import React, {Component} from "react";
import MetadataForm from "./MetadataForm";
import FormItem from "../FormItem";
import LocalizedTextInput from "components/Input/Formik/LocalizedTextInput";
import {Field} from "formik";
import OntologyConceptFormBlock from "components/Input/Formik/OntologyConceptFormBlock";

type DatasetMetadataFormProps = {
    dataset: any,
    onSave: () => void,
}

export default class DatasetMetadataForm extends Component<DatasetMetadataFormProps> {
    render() {
        const { dataset, onSave } = this.props;

        return <MetadataForm type="dataset" object={dataset} onSave={onSave}
                             defaultData={defaultData}
        >
            {(validation, languages) => (<div>
                <FormItem label="Keywords">
                    <Field
                        component={LocalizedTextInput}
                        name="keyword"
                        serverError={validation}
                        languages={languages}
                    />
                </FormItem>

                <Field
                    component={OntologyConceptFormBlock}
                    label="Themes"
                    name="theme"
                />
            </div>)}
        </MetadataForm>;
    }
}

const defaultData = {
    'theme': [],
    'keyword': [{
        text: '',
        language: null
    }],
};
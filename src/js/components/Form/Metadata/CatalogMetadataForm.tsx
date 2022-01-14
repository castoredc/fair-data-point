import React, {Component} from 'react';
import '../Form.scss'
import MetadataForm from "./MetadataForm";
import FormItem from "../FormItem";
import {Field} from "formik";
import OntologyConceptFormBlock from "components/Input/Formik/OntologyConceptFormBlock";
import Input from "components/Input/Formik/Input";

type CatalogMetadataFormProps = {
    catalog: any,
    onSave: () => void,
}

export default class CatalogMetadataForm extends Component<CatalogMetadataFormProps> {
    render() {
        const {catalog, onSave} = this.props;

        return (
            <MetadataForm
                type="catalog"
                object={catalog}
                onSave={onSave}
                defaultData={defaultData}
            >{(validation, languages) => (<div>
                <FormItem label="Homepage">
                    <Field
                        component={Input}
                        name="homepage"
                        serverError={validation}
                    />
                </FormItem>

                <FormItem label="Logo">
                    <Field
                        component={Input}
                        name="logo"
                        serverError={validation}
                    />
                </FormItem>

                <Field
                    component={OntologyConceptFormBlock}
                    label="Theme taxonomy"
                    name="themeTaxonomy"
                />
            </div>)}
            </MetadataForm>
        );
    }
}

const defaultData = {
    'homepage': '',
    'logo': '',
    'themeTaxonomy': []
};
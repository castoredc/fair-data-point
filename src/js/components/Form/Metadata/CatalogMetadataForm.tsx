import React, { Component } from 'react';
import '../Form.scss';
import MetadataForm from './MetadataForm';
import FormItem from '../FormItem';
import { Field } from 'formik';
import OntologyConceptFormBlock from 'components/Input/Formik/OntologyConceptFormBlock';
import Input from 'components/Input/Formik/Input';

type CatalogMetadataFormProps = {
    catalog: any;
    onSave: () => void;
};

export default class CatalogMetadataForm extends Component<CatalogMetadataFormProps> {
    render() {
        const { catalog, onSave } = this.props;

        return (
            <MetadataForm type="catalog" object={catalog} onSave={onSave} defaultData={defaultData}>
                {(validation, languages) => (
                    <div>
                        <FormItem label="Homepage" details="The homepage of the catalog (URL)">
                            <Field component={Input} name="homepage" serverError={validation} />
                        </FormItem>

                        <FormItem label="Logo" details="A URL pointing to the logo of the catalog">
                            <Field component={Input} name="logo" serverError={validation} />
                        </FormItem>

                        <Field
                            component={OntologyConceptFormBlock}
                            label="Theme taxonomy"
                            name="themeTaxonomy"
                            details="Themes (ontology concepts) used to classify the cataloged resources that are part of this catalog."
                        />
                    </div>
                )}
            </MetadataForm>
        );
    }
}

const defaultData = {
    homepage: '',
    logo: '',
    themeTaxonomy: [],
};

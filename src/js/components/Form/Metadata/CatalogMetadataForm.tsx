import React, { Component } from 'react';
import '../Form.scss';
import LegacyMetadataForm from './LegacyMetadataForm';
import FormItem from '../FormItem';
import { Field } from 'formik';
import OntologyConceptFormBlock from 'components/Input/Formik/OntologyConceptFormBlock';
import Input from 'components/Input/Formik/Input';
import MetadataForm from 'components/Form/Metadata/MetadataForm';

type CatalogMetadataFormProps = {
    catalog: any;
    onCreate: () => void;
    onSave: () => void;
};

export default class CatalogMetadataForm extends Component<CatalogMetadataFormProps> {
    render() {
        const { catalog, onCreate, onSave } = this.props;

        return (
            <MetadataForm
                type="catalog"
                object={catalog}
                onCreate={onCreate}
                onSave={onSave}
            />
        );
    }
}

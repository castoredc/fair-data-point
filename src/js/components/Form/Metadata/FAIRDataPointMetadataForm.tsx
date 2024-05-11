import React, { Component } from 'react';
import LegacyMetadataForm from './LegacyMetadataForm';

type FAIRDataPointMetadataFormProps = {
    fdp: any;
    onSave: () => void;
};

export default class FAIRDataPointMetadataForm extends Component<FAIRDataPointMetadataFormProps> {
    render() {
        const { fdp, onSave } = this.props;

        return <LegacyMetadataForm type="fdp" object={fdp} onSave={onSave} />;
    }
}

import React, { Component } from 'react';
import LegacyMetadataForm from './LegacyMetadataForm';

type DistributionMetadataFormProps = {
    distribution: any;
    onSave: () => void;
};

export default class DistributionMetadataForm extends Component<DistributionMetadataFormProps> {
    render() {
        const { distribution, onSave } = this.props;

        return <LegacyMetadataForm type="distribution" object={distribution} onSave={onSave} />;
    }
}

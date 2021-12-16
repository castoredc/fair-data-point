import React, {Component} from "react";
import MetadataForm from "./MetadataForm";

type DistributionMetadataFormProps = {
    distribution: any,
    onSave: () => void,
}

export default class DistributionMetadataForm extends Component<DistributionMetadataFormProps> {
    render() {
        const { distribution, onSave } = this.props;

        return <MetadataForm type="distribution" object={distribution} onSave={onSave} />;
    }
}
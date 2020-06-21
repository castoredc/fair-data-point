import React, {Component} from "react";
import MetadataForm from "./MetadataForm";

export default class DistributionMetadataForm extends Component {
    render() {
        const { distribution, onSave } = this.props;

        return <MetadataForm type="distribution" object={distribution} onSave={onSave} />;
    }
}
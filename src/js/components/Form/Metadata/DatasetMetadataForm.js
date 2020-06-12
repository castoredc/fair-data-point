import React, {Component} from "react";
import MetadataForm from "./MetadataForm";

export default class DatasetMetadataForm extends Component {
    render() {
        const { dataset, onSave } = this.props;

        return <MetadataForm type="dataset" object={dataset} onSave={onSave} />;
    }
}
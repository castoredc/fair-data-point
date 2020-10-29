import React, {Component} from "react";
import MetadataForm from "./MetadataForm";

export default class FAIRDataPointMetadataForm extends Component {
    render() {
        const { fdp, onSave } = this.props;

        return <MetadataForm type="fdp" object={fdp} onSave={onSave} />;
    }
}
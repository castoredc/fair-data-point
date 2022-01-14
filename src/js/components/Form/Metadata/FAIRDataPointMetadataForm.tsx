import React, {Component} from "react";
import MetadataForm from "./MetadataForm";

type FAIRDataPointMetadataFormProps = {
    fdp: any,
    onSave: () => void,
}

export default class FAIRDataPointMetadataForm extends Component<FAIRDataPointMetadataFormProps> {
    render() {
        const {fdp, onSave} = this.props;

        return <MetadataForm type="fdp" object={fdp} onSave={onSave}/>;
    }
}
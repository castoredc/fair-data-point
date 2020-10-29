import React, {Component} from "react";
import FAIRDataPointMetadataForm from "../../../components/Form/Metadata/FAIRDataPointMetadataForm";

export default class FAIRDataPointMetadata extends Component {
    render() {
        const { fdp, onSave } = this.props;

        return <div className="PageBody">
            <FAIRDataPointMetadataForm fdp={fdp} onSave={onSave} />
        </div>;
    }

}
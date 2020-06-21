import React, {Component} from "react";
import DatasetMetadataForm from "../../../components/Form/Metadata/DatasetMetadataForm";

export default class DatasetMetadata extends Component {
    render() {
        const { dataset, onSave } = this.props;

        return <div>
            <DatasetMetadataForm dataset={dataset} onSave={onSave} />
        </div>;
    }

}
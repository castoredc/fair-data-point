import React, {Component} from "react";
import CatalogMetadataForm from "../../../components/Form/Metadata/CatalogMetadataForm";

export default class CatalogMetadata extends Component {
    render() {
        const { catalog, onSave } = this.props;

        return <div className="PageBody">
            <CatalogMetadataForm catalog={catalog} onSave={onSave} />
        </div>;
    }

}
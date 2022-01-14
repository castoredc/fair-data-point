import React, {Component} from "react";
import DistributionMetadataForm from "components/Form/Metadata/DistributionMetadataForm";

export default class DistributionMetadata extends Component {
    render() {
        const {distribution, onSave} = this.props;

        return <div className="PageBody">
            <DistributionMetadataForm distribution={distribution} onSave={onSave}/>
        </div>;
    }

}
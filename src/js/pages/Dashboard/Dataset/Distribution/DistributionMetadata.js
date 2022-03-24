import React, {Component} from "react";
import DistributionMetadataForm from "components/Form/Metadata/DistributionMetadataForm";
import PageBody from "components/Layout/Dashboard/PageBody";

export default class DistributionMetadata extends Component {
    render() {
        const {distribution, onSave} = this.props;

        return <PageBody>
            <DistributionMetadataForm distribution={distribution} onSave={onSave}/>
        </PageBody>;
    }

}
import React, {Component} from "react";
import DistributionForm from "../../../components/Form/Admin/Distribution/DistributionForm";

export default class DistributionDetails extends Component {
    render() {
        const { distribution, catalog, dataset } = this.props;

        return <div>
            <DistributionForm
                catalog={catalog}
                dataset={dataset}
                distribution={distribution}
            />
        </div>;
    }
}
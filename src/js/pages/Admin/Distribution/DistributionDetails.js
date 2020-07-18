import React, {Component} from "react";
import DistributionForm from "../../../components/Form/Admin/DistributionForm";

export default class DistributionDetails extends Component {
    render() {
        const { distribution, catalog, dataset } = this.props;

        return <div className="PageBody">
            <DistributionForm
                catalog={catalog}
                dataset={dataset}
                distribution={distribution}
            />
        </div>;
    }
}
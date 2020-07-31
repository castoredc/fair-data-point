import React, {Component} from "react";
import DistributionLogsDataTable from "../../../components/DataTable/DistributionLogsDataTable";

export default class DistributionLogs extends Component {
    render() {
        const {dataset, distribution, history} = this.props;

        return <div className="PageBody">
            <DistributionLogsDataTable
                history={history}
                dataset={dataset}
                distribution={distribution}
            />
        </div>;
    }
}
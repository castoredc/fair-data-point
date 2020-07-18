import React, {Component} from "react";
import DatasetsDataTable from "../../../components/DataTable/DatasetsDataTable";

export default class CatalogDatasets extends Component {
    render() {
        const {catalog, history} = this.props;

        return <div className="PageBody">
            <DatasetsDataTable
                history={history}
                catalog={catalog}
            />
        </div>;
    }
}
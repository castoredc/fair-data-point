import React, {Component} from "react";
import DatasetForm from "../../../components/Form/Admin/DatasetForm";

export default class DatasetDetails extends Component {
    render() {
        const { dataset } = this.props;

        return <div className="PageBody">
            <DatasetForm
                dataset={dataset}
            />
        </div>;
    }

}
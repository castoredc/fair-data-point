import React, {Component} from "react";
import DatasetForm from "../../../components/Form/Admin/DatasetForm";

export default class DatasetDetails extends Component {
    render() {
        const { dataset } = this.props;

        return <div>
            <DatasetForm
                dataset={dataset}
            />
        </div>;
    }

}
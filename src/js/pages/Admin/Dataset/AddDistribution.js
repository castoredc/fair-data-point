import React, {Component} from "react";
import DistributionForm from "../../../components/Form/Admin/Distribution/DistributionForm";

export default class AddDistribution extends Component {
    render() {
        const { catalog, dataset } = this.props;

        return <DistributionForm catalog={catalog} dataset={dataset.slug} />
    }
}
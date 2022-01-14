import React, {Component} from "react";
import {RouteComponentProps} from "react-router-dom";
import DistributionForm from "components/Form/Admin/DistributionForm";

interface AddDistributionProps extends RouteComponentProps<any> {
}

export default class AddDistribution extends Component<AddDistributionProps> {
    constructor(props) {
        super(props);

        this.state = {};
    }

    render() {
        const {match, history} = this.props;

        const mainUrl = match.params.study ? `/dashboard/studies/${match.params.study}/datasets/${match.params.dataset}` : `/dashboard/catalogs/${match.params.catalog}/datasets/${match.params.dataset}`;

        return <DistributionForm
            dataset={match.params.dataset}
            mainUrl={mainUrl}
            history={history}
        />;
    }
}

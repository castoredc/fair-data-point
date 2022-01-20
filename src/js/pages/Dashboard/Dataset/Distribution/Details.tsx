import React, {Component} from "react";
import DistributionForm from "components/Form/Admin/DistributionForm";
import {AuthorizedRouteComponentProps} from "components/Route";

interface DetailsProps extends AuthorizedRouteComponentProps {
    distribution: any,
    catalog?: string,
    dataset?: string,
    study?: string,
}

export default class Details extends Component<DetailsProps> {
    render() {
        const {distribution, catalog, dataset, study, history} = this.props;

        const mainUrl = study ? `/dashboard/studies/${study}/datasets/${dataset}` : `/dashboard/catalogs/${catalog}/datasets/${dataset}`;

        return <div className="PageBody">
            <DistributionForm
                history={history}
                mainUrl={mainUrl}
                dataset={dataset}
                distribution={distribution}
            />
        </div>;
    }
}
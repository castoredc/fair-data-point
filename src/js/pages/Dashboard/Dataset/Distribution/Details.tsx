import React, { Component } from 'react';
import DistributionForm from 'components/Form/Admin/DistributionForm';
import { AuthorizedRouteComponentProps } from 'components/Route';
import PageBody from 'components/Layout/Dashboard/PageBody';

interface DetailsProps extends AuthorizedRouteComponentProps {
    distribution: any;
    catalog?: string;
    dataset?: string;
    study?: string;
}

class Details extends Component<DetailsProps> {
    render() {
        const { distribution, catalog, dataset, study, history } = this.props;

        const mainUrl = study ? `/dashboard/studies/${study}/datasets/${dataset}` : `/dashboard/catalogs/${catalog}/datasets/${dataset}`;

        return (
            <PageBody>
                <DistributionForm history={history} mainUrl={mainUrl} dataset={dataset} distribution={distribution} />
            </PageBody>
        );
    }
}

export default Details;
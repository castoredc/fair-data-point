import React, { Component } from 'react';
import DistributionForm from 'components/Form/Admin/DistributionForm';
import { AuthorizedRouteComponentProps } from 'components/Route';
import PageBody from 'components/Layout/Dashboard/PageBody';

interface AddDistributionProps extends AuthorizedRouteComponentProps {
}

class AddDistribution extends Component<AddDistributionProps> {
    constructor(props) {
        super(props);

        this.state = {};
    }

    render() {
        const { match, history } = this.props;

        const mainUrl = match.params.study
            ? `/dashboard/studies/${match.params.study}/datasets/${match.params.dataset}`
            : `/dashboard/catalogs/${match.params.catalog}/datasets/${match.params.dataset}`;

        return (
            <PageBody>
                <DistributionForm dataset={match.params.dataset} mainUrl={mainUrl} history={history} />
            </PageBody>
        );
    }
}

export default AddDistribution;
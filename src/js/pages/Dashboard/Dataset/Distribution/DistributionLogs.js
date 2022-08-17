import React, { Component } from 'react';
import DistributionLogsDataTable from 'components/DataTable/DistributionLogsDataTable';
import PageBody from 'components/Layout/Dashboard/PageBody';

export default class DistributionLogs extends Component {
    render() {
        const { dataset, study, catalog, distribution, history } = this.props;

        return (
            <PageBody>
                <DistributionLogsDataTable history={history} catalog={catalog} study={study} dataset={dataset} distribution={distribution} />
            </PageBody>
        );
    }
}

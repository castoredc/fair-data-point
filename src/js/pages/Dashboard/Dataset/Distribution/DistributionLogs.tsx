import React from 'react';
import DistributionLogsDataTable from 'components/DataTable/DistributionLogsDataTable';
import PageBody from 'components/Layout/Dashboard/PageBody';
import * as H from 'history';

interface DistributionLogsProps {
    dataset: string;
    study: any;
    catalog: any;
    distribution: any;
    history: H.History;
}

const DistributionLogs: React.FC<DistributionLogsProps> = ({ dataset, study, catalog, distribution, history }) => {
    return (
        <PageBody>
            <DistributionLogsDataTable history={history} catalog={catalog} study={study} dataset={dataset}
                                       distribution={distribution} />
        </PageBody>
    );
};

export default DistributionLogs;

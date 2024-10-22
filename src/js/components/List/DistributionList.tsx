import React from 'react';
import { CommonListProps } from 'components/List/types';
import List from 'components/List/List';

const DistributionList: React.FC<CommonListProps> = (props) => {
    const { dataset, agent } = props;

    let apiEndpoint = '/api/distribution';
    let noResultsText = 'This FAIR Data Point does not have any associated distributions.';

    if (agent) {
        apiEndpoint = `/api/agent/details/${agent.slug}/distribution`;
        noResultsText = 'This agent does not have any associated distributions.';
    } else if (dataset) {
        apiEndpoint = `/api/dataset/${dataset.slug}/distribution`;
        noResultsText = 'This dataset does not have any associated distributions.';
    }

    return <List
        {...props}
        apiEndpoint={apiEndpoint}
        filterFunction={item => item.hasMetadata}
        noResultsText={noResultsText}
    />;
};

export default DistributionList;

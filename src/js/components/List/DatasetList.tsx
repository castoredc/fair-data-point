import React from 'react';
import { CommonListProps } from 'components/List/types';
import List from 'components/List/List';

const DatasetList: React.FC<CommonListProps> = props => {
    const { catalog, study, agent } = props;
    let apiEndpoint = '';
    let noResultsText = '';

    if (study) {
        apiEndpoint = `/api/study/${study.id}/dataset`;
        noResultsText = 'This study does not have any associated datasets.';
    } else if (catalog) {
        apiEndpoint = `/api/catalog/${catalog.slug}/dataset`;
        noResultsText = 'This catalog does not have any associated datasets.';
    } else if (agent) {
        apiEndpoint = `/api/agent/details/${agent.slug}/dataset`;
        noResultsText = 'This agent does not have any associated datasets.';
    }

    return <List {...props} apiEndpoint={apiEndpoint} filterFunction={item => item.hasMetadata}
                 noResultsText={noResultsText} />;
};

export default DatasetList;

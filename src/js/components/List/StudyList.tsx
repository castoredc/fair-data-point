import React from 'react';
import List from './List';
import { CommonListProps } from 'components/List/types';

const StudyList: React.FC<CommonListProps> = props => {
    const { catalog, agent } = props;

    let apiEndpoint = '/api/study';
    let noResultsText = 'This FAIR Data Point does not have any associated studies.';

    if (catalog) {
        apiEndpoint = `/api/catalog/${catalog.slug}/study`;
        noResultsText = 'This catalog does not have any associated studies.';
    } else if (agent) {
        apiEndpoint = `/api/agent/details/${agent.slug}/study`;
        noResultsText = 'This agent does not have any associated studies.';
    }

    return <List {...props} apiEndpoint={apiEndpoint} filterFunction={item => item.hasMetadata} noResultsText={noResultsText} />;
};

export default StudyList;

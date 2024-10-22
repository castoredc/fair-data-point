import React from 'react';
import List from './List';
import { CommonListProps } from 'components/List/types';

const CatalogList: React.FC<CommonListProps> = props => {
    const { agent } = props;

    let apiEndpoint = '/api/catalog';
    let noResultsText = 'This FAIR Data Point does not have any associated catalogs.';

    if (agent) {
        apiEndpoint = `/api/agent/details/${agent.slug}/catalog`;
        noResultsText = 'This agent does not have any associated catalogs.';
    }

    return <List {...props} apiEndpoint={apiEndpoint} filterFunction={item => item.hasMetadata} noResultsText={noResultsText} />;
};

export default CatalogList;

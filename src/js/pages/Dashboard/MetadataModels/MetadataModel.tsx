import React, { FunctionComponent } from 'react';
import { AuthorizedRouteComponentProps } from 'components/Route';
import DataSpecification from 'pages/Dashboard/DataSpecification';

interface MetadataModelProps extends AuthorizedRouteComponentProps {}
export const MetadataModel: FunctionComponent<MetadataModelProps> = ({ history, location, match, user }) => {
    return <DataSpecification type="metadata-model" history={history} location={location} match={match} user={user} />;
};

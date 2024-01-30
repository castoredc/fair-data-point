import React, { FunctionComponent } from 'react';
import { AuthorizedRouteComponentProps } from 'components/Route';
import DataSpecification from 'pages/Dashboard/DataSpecification';

interface DataModelProps extends AuthorizedRouteComponentProps {}
export const DataModel: FunctionComponent<DataModelProps> = ({ history, location, match, user }) => {
    return (
        <DataSpecification
            type="data-model"
            history={history}
            location={location}
            match={match}
            user={user}
        />
    );
};
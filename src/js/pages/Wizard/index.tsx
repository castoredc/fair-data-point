import React, { FC } from 'react';
import { Route, Switch } from 'react-router-dom';
import NotFound from 'pages/ErrorPages/NotFound';
import UserDetails from './UserDetails';
import Affiliations from './Affiliations';
import { AuthorizedRouteComponentProps } from 'components/Route';
import WizardLayout from 'components/Layout/WizardLayout';

interface WizardProps extends AuthorizedRouteComponentProps {
}

const Wizard: FC<WizardProps> = ({ user, ...rest }) => {
    return (
        <WizardLayout>
            <Switch>
                <Route
                    path="/wizard/user/details"
                    exact
                    render={props => <UserDetails {...rest} user={user} />}
                />
                <Route
                    path="/wizard/user/affiliations"
                    exact
                    render={props => <Affiliations {...rest} user={user} />}
                />
                <Route component={NotFound} />
            </Switch>
        </WizardLayout>
    );
};

export default Wizard;

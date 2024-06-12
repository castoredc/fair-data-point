import React, { FC } from 'react';
import { Route, Switch } from 'react-router-dom';
import FAIRDataPointMain from './FAIRDataPointMain';
import Study from './Study';
import Catalog from './Catalog';
import Dataset from './Dataset';
import Distribution from './Distribution';
import Query from './Query';
import Agent from './Agent';
import { UserType } from 'types/UserType';

interface MainProps {
    user: UserType | null;
    embedded: boolean;
}

const Main: FC<MainProps> = ({ user, embedded }) => {
    return (
        <Switch>
            <Route
                path="/fdp"
                exact
                render={props => <FAIRDataPointMain {...props} embedded={embedded} user={user} />}
            />
            <Route
                path="/study/:study"
                exact
                render={props => <Study {...props} embedded={embedded} user={user} />}
            />
            <Route
                path="/fdp/person/:slug"
                exact
                render={props => <Agent {...props} type="person" embedded={embedded} user={user} />} />
            <Route
                path="/fdp/organization/:slug"
                exact
                render={props => <Agent {...props} type="organization" embedded={embedded} user={user} />}
            />
            <Route
                path="/fdp/catalog/:catalog"
                render={props => <Catalog {...props} embedded={embedded} user={user} />}
            />
            <Route
                path="/fdp/dataset/:dataset"
                exact
                render={props => <Dataset {...props} embedded={embedded} user={user} />}
            />
            <Route
                path="/fdp/dataset/:dataset/distribution/:distribution"
                exact
                render={props => <Distribution {...props} embedded={embedded} user={user} />}
            />
            /* Query */
            <Route
                path="/fdp/dataset/:dataset/distribution/:distribution/query"
                exact
                render={props => <Query embedded={embedded} user={user} />}
            />
        </Switch>
    );
};

export default Main;
import React, { Component, FunctionComponent } from 'react';
import { Redirect, Route, Switch } from 'react-router-dom';
import NotFound from 'pages/ErrorPages/NotFound';
import { CastorBar, Tabs } from '@castoredc/matter';
import Studies from './Studies';
import './Dashboard.scss';
import { toRem } from '@castoredc/matter-utils';
import AddStudy from 'pages/Dashboard/Studies/AddStudy';
import DataModels from 'pages/Dashboard/DataModels';
import Study from 'pages/Dashboard/Studies/Study';
import Dataset from 'pages/Dashboard/Dataset';
import Distribution from 'pages/Dashboard/Dataset/Distribution';
import Catalogs from 'pages/Dashboard/Catalogs';
import Catalog from 'pages/Dashboard/Catalogs/Catalog';
import FAIRDataPoint from 'pages/Dashboard/FAIRDataPoint';
import { EDCServers } from 'pages/Dashboard/EDCServers';
import { AuthorizedRouteComponentProps, PrivateRoute, ProtectedRoute } from 'components/Route';
import { isAdmin } from 'utils/PermissionHelper';
import SelectCatalog from 'pages/Dashboard/Studies/SelectCatalog';
import DataModel from 'pages/Dashboard/DataModels/DataModel';
import AddCatalog from 'pages/Dashboard/Catalogs/AddCatalog';
import AddDataModel from 'pages/Dashboard/DataModels/AddDataModel';
import AddDataset from 'pages/Dashboard/Catalogs/Catalog/AddDataset';

interface DashboardProps extends AuthorizedRouteComponentProps {}

const DashboardTabs: FunctionComponent<DashboardProps> = ({ history, location, match, user }) => {
    const urls = {
        '/dashboard/studies': 'studies',
        '/dashboard/catalogs': 'catalogs',
        '/dashboard/data-models': 'dataModels',
        '/dashboard/fdp': 'fdp',
        '/dashboard/edc-servers': 'edcServers',
    };

    const tabs = {
        studies: {
            content: <Studies history={history} location={location} match={match} user={user} />,
            title: 'Studies',
        },
        catalogs: {
            content: <Catalogs history={history} location={location} match={match} user={user} />,
            title: 'Catalogs',
        },
        dataModels: {
            content: <DataModels history={history} location={location} match={match} user={user} />,
            title: 'Data models',
        },
        ...(isAdmin(user) && {
            fdp: {
                content: <FAIRDataPoint history={history} location={location} match={match} user={user} />,
                title: 'FAIR Data Point',
            },
            edcServers: {
                content: <EDCServers history={history} location={location} match={match} user={user} />,
                title: 'EDC Servers',
            },
        }),
    };

    return (
        <div className="DashboardTabs">
            <div
                style={{
                    width: toRem(960),
                    maxWidth: '100%',
                    padding: '3.2rem 3.2rem 0 3.2rem',
                }}
            >
                <Tabs
                    tabs={tabs}
                    selected={urls[location.pathname]}
                    onChange={selectedKey => {
                        const newUrl = Object.keys(urls).find(key => urls[key] === selectedKey) ?? '/dashboard/studies';
                        history.push(newUrl);
                    }}
                />
            </div>
        </div>
    );
};

export default class Dashboard extends Component<DashboardProps> {
    constructor(props) {
        super(props);
    }

    render() {
        const { history, user } = this.props;

        return (
            <div className="Dashboard" style={{ paddingTop: '4.8rem' }}>
                <CastorBar
                    items={[
                        {
                            destination: () => history.push('/dashboard/studies'),
                            label: 'Castor',
                            type: 'brand',
                        },
                        {
                            items: [
                                {
                                    isTitle: true,
                                    label: 'Account',
                                },
                                {
                                    destination: '/logout',
                                    icon: 'logOut',
                                    label: 'Log out',
                                },
                            ],
                            label: 'Account',
                            type: 'account',
                        },
                    ]}
                    label="Castor navigation"
                    horizontalNav
                />
                <div className="Main">
                    <Switch>
                        <Redirect exact from="/dashboard" to="/dashboard/studies" />

                        <PrivateRoute path="/dashboard/studies" exact routeComponent={DashboardTabs} user={user} />

                        <PrivateRoute path="/dashboard/studies/add" exact routeComponent={SelectCatalog} user={user} />
                        <PrivateRoute path="/dashboard/studies/add/:catalog" exact routeComponent={AddStudy} user={user} />

                        <PrivateRoute path="/dashboard/studies/:study/datasets/:dataset/distributions/add" routeComponent={Dataset} user={user} />
                        <PrivateRoute
                            path="/dashboard/studies/:study/datasets/:dataset/distributions/:distribution"
                            routeComponent={Distribution}
                            user={user}
                        />
                        <PrivateRoute path="/dashboard/studies/:study/datasets/:dataset" routeComponent={Dataset} user={user} />
                        <PrivateRoute path="/dashboard/studies/:study" routeComponent={Study} user={user} />

                        <ProtectedRoute path="/dashboard/catalogs/add" exact routeComponent={AddCatalog} user={user} />
                        <PrivateRoute path="/dashboard/catalogs/:catalog/datasets/:dataset/distributions/add" routeComponent={Dataset} user={user} />
                        <PrivateRoute
                            path="/dashboard/catalogs/:catalog/datasets/:dataset/distributions/:distribution"
                            routeComponent={Distribution}
                            user={user}
                        />
                        <PrivateRoute path="/dashboard/catalogs/:catalog/datasets/add" exact routeComponent={Catalog} user={user} />
                        <PrivateRoute path="/dashboard/catalogs/:catalog/datasets/:dataset" routeComponent={Dataset} user={user} />
                        <PrivateRoute path="/dashboard/catalogs" exact routeComponent={DashboardTabs} user={user} />
                        <PrivateRoute path="/dashboard/catalogs/:catalog" routeComponent={Catalog} user={user} />

                        <ProtectedRoute path="/dashboard/fdp" exact routeComponent={DashboardTabs} user={user} />
                        <ProtectedRoute path="/dashboard/edc-servers" exact routeComponent={DashboardTabs} user={user} />

                        <PrivateRoute path="/dashboard/data-models" exact routeComponent={DashboardTabs} user={user} />
                        <PrivateRoute path="/dashboard/data-models/add" exact routeComponent={AddDataModel} user={user} />

                        <PrivateRoute path="/dashboard/data-models/:model/:version" routeComponent={DataModel} user={user} />
                        <PrivateRoute path="/dashboard/data-models/:model" routeComponent={DataModel} user={user} />

                        <Route component={NotFound} />
                    </Switch>
                </div>
            </div>
        );
    }
}

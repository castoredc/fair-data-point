import React, { Component } from 'react';
import { UserType } from 'types/UserType';
import { Redirect, Route, Switch } from 'react-router-dom';
import { PrivateRoute, ProtectedRoute } from 'components/Route';
import SelectCatalog from 'pages/Dashboard/Studies/SelectCatalog';
import AddStudy from 'pages/Dashboard/Studies/AddStudy';
import Dataset from 'pages/Dashboard/Dataset';
import Distribution from 'pages/Dashboard/Dataset/Distribution';
import Study from 'pages/Dashboard/Studies/Study';
import AddCatalog from 'pages/Dashboard/Catalogs/AddCatalog';
import Catalog from 'pages/Dashboard/Catalogs/Catalog';
import AddDataModel from 'pages/Dashboard/DataModels/AddDataModel';
import NotFound from 'pages/ErrorPages/NotFound';
import AddMetadataModel from 'pages/Dashboard/MetadataModels/AddMetadataModel';
import { DataModel } from 'pages/Dashboard/DataModels/DataModel';
import { MetadataModel } from 'pages/Dashboard/MetadataModels/MetadataModel';
import Studies from 'pages/Dashboard/Studies';
import Catalogs from 'pages/Dashboard/Catalogs';
import DataModels from 'pages/Dashboard/DataModels';
import MetadataModels from 'pages/Dashboard/MetadataModels';
import FAIRDataPoint from 'pages/Dashboard/FAIRDataPoint';
import EDCServers from 'pages/Dashboard/EDCServers';

export class DashboardRoutes extends Component<{ user: UserType | null }> {
    render() {
        const { user } = this.props;

        return (
            <Switch>
                <Redirect exact from="/dashboard" to="/dashboard/studies" />

                <PrivateRoute path="/dashboard/studies" exact routeComponent={Studies} user={user} />

                <PrivateRoute path="/dashboard/studies/add" exact routeComponent={SelectCatalog} user={user} />
                <PrivateRoute path="/dashboard/studies/add/:catalog" exact routeComponent={AddStudy} user={user} />

                <PrivateRoute path="/dashboard/studies/:study/datasets/:dataset/distributions/add"
                              routeComponent={Dataset} user={user} />
                <PrivateRoute
                    path="/dashboard/studies/:study/datasets/:dataset/distributions/:distribution"
                    routeComponent={Distribution}
                    user={user}
                />
                <PrivateRoute path="/dashboard/studies/:study/datasets/:dataset" routeComponent={Dataset} user={user} />
                <PrivateRoute path="/dashboard/studies/:study" routeComponent={Study} user={user} />

                <ProtectedRoute path="/dashboard/catalogs/add" exact routeComponent={AddCatalog} user={user} />
                <PrivateRoute path="/dashboard/catalogs/:catalog/datasets/:dataset/distributions/add"
                              routeComponent={Dataset} user={user} />
                <PrivateRoute
                    path="/dashboard/catalogs/:catalog/datasets/:dataset/distributions/:distribution"
                    routeComponent={Distribution}
                    user={user}
                />
                <PrivateRoute path="/dashboard/catalogs/:catalog/datasets/add" exact routeComponent={Catalog}
                              user={user} />
                <PrivateRoute path="/dashboard/catalogs/:catalog/datasets/:dataset" routeComponent={Dataset}
                              user={user} />
                <PrivateRoute path="/dashboard/catalogs" exact routeComponent={Catalogs} user={user} />
                <PrivateRoute path="/dashboard/catalogs/:catalog" routeComponent={Catalog} user={user} />

                <ProtectedRoute path="/dashboard/fdp" exact routeComponent={FAIRDataPoint} user={user} />
                <ProtectedRoute path="/dashboard/edc-servers" exact routeComponent={EDCServers} user={user} />

                <PrivateRoute path="/dashboard/data-models" exact routeComponent={DataModels} user={user} />
                <PrivateRoute path="/dashboard/data-models/add" exact routeComponent={AddDataModel} user={user} />

                <PrivateRoute path="/dashboard/data-models/:model/:version" routeComponent={DataModel} user={user} />
                <PrivateRoute path="/dashboard/data-models/:model" routeComponent={DataModel} user={user} />

                <PrivateRoute path="/dashboard/metadata-models" exact routeComponent={MetadataModels} user={user} />
                <PrivateRoute path="/dashboard/metadata-models/add" exact routeComponent={AddMetadataModel}
                              user={user} />

                <PrivateRoute path="/dashboard/metadata-models/:model/:version" routeComponent={MetadataModel}
                              user={user} />
                <PrivateRoute path="/dashboard/metadata-models/:model" routeComponent={MetadataModel} user={user} />

                <Route component={NotFound} />
            </Switch>
        );
    }
}

import React from "react";
import {Redirect, Route, Switch} from "react-router-dom";
import NotFound from "./pages/NotFound";
import FAIRDataPointMain from "./pages/FAIRDataPoint/FAIRDataPointMain";
import Catalog from "./pages/FAIRDataPoint/Catalog";
import Dataset from "./pages/FAIRDataPoint/Dataset";
import Distribution from "./pages/FAIRDataPoint/Distribution";
import MyStudies from "./pages/StudyMetadata/MyStudies";
import Login from "./pages/Login";
import SelectStudy from "./pages/StudyMetadata/SelectStudy";
import EditStudyDetails from "./pages/StudyMetadata/EditStudyDetails";
import EditOrganizationDetails from "./pages/StudyMetadata/EditOrganizationDetails";
// import Query from "./pages/Query";

const PrivateRoute = ({ component: Component, path, user, ...rest }) => (
    <Route {...rest} render={(props) => (
        user !== null
            ? <Component {...props} />
            : <Redirect to={{
                pathname: '/login',
                search:   '?path=' + encodeURI(path)
            }} />
    )} />
);

export default ({user}) =>
    <Switch>
        <Redirect exact from="/" to="/fdp" />
        <Route path="/login" exact component={Login} />

        /* FAIR Data Point */
        <Route path="/fdp" exact component={FAIRDataPointMain} />
        {/*<Route path="/query" exact component={Query} />*/}
        <Route path="/fdp/:catalogSlug" exact component={Catalog} />
        <Route path="/fdp/:catalogSlug/:datasetSlug" exact component={Dataset} />
        <Route path="/fdp/:catalogSlug/:datasetSlug/:distributionSlug" exact component={Distribution} />

        /* My studies */
        <PrivateRoute path="/my-studies" exact user={user} component={MyStudies} />
        <PrivateRoute path="/my-studies/study/add" exact user={user} component={SelectStudy} />
        <PrivateRoute path="/my-studies/study/:studyId/metadata/details" exact user={user} component={EditStudyDetails} />
        <PrivateRoute path="/my-studies/study/:studyId/metadata/organizations" exact user={user} component={EditOrganizationDetails} />
        {/*<PrivateRoute path="/my-studies/study/:studyId/metadata/contact" exact user={user} component={EditStudy} />*/}

        <Route component={NotFound} />
    </Switch>;
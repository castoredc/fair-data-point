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
import axios from "axios";
import EditContactDetails from "./pages/StudyMetadata/EditContactDetails";
import Finished from "./pages/StudyMetadata/Finished";
import PrivateRoute from './components/PrivateRoute';
import StudyMetadataWrapper from "./pages/StudyMetadata/StudyMetadataWrapper";

axios.interceptors.response.use(function (response) {
    return response;
}, function (error) {
    if (401 === error.response.status) {
        window.location.href = '/login?path' + encodeURIComponent(window.location.pathname);
    } else {
        return Promise.reject(error);
    }
});

export default ({user}) =>
    <Switch>
        <Redirect exact from="/" to="/fdp" />
        <Route path="/login" exact component={Login} />
        <Route path="/login/:catalogSlug" exact component={Login} />

        /* FAIR Data Point */
        <Route path="/fdp" exact component={FAIRDataPointMain} />
        {/*<Route path="/query" exact component={Query} />*/}
        <Route path="/fdp/:catalogSlug" exact component={Catalog} />
        <Route path="/fdp/:catalogSlug/:datasetSlug" exact component={Dataset} />
        <Route path="/fdp/:catalogSlug/:datasetSlug/:distributionSlug" exact component={Distribution} />

        /* My studies */
        {/*<PrivateRoute path="/my-studies" exact user={user} component={MyStudies} />*/}
        <PrivateRoute path="/my-studies/:catalog/study/add" exact user={user} component={StudyMetadataWrapper} />
        <PrivateRoute path="/my-studies/:catalog/study/:studyId/metadata/details" exact user={user} component={StudyMetadataWrapper} />
        <PrivateRoute path="/my-studies/:catalog/study/:studyId/metadata/centers" exact user={user} component={StudyMetadataWrapper} />
        <PrivateRoute path="/my-studies/:catalog/study/:studyId/metadata/contacts" exact user={user} component={StudyMetadataWrapper} />
        <PrivateRoute path="/my-studies/:catalog/study/:studyId/metadata/finished" exact user={user} component={StudyMetadataWrapper} />

        <Route component={NotFound} />
    </Switch>;
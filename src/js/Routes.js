import React from "react";
import {Redirect, Route, Switch} from "react-router-dom";
import NotFound from "./pages/NotFound";
import Login from "./pages/Login";
import axios from "axios";
import StudyMetadataWrapper from "./pages/StudyMetadata/StudyMetadataWrapper";
import {PrivateRoute, ProtectedRoute} from "./components/Route";
import AdminPageWrapper from "./pages/Admin/PageWrapper";
import Main from "./pages/Main";

axios.interceptors.response.use(function (response) {
    return response;
}, function (error) {
    if (axios.isCancel(error)) {
        return Promise.reject(error);
    }
    else if (401 === error.response.status) {
        window.location.href = '/login?path=' + encodeURIComponent(window.location.pathname);
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
        <Route path="/fdp" render={(props) => <Main {...props} user={user} />} />
        <Route path="/study" render={(props) => <Main {...props} user={user} />} />
        <Route path="/tools" render={(props) => <Main {...props} user={user} />} />

        /* My studies */
        {/*<PrivateRoute path="/my-studies" exact user={user} component={MyStudies} />*/}
        <PrivateRoute path="/my-studies/:catalog/study/add" exact user={user} component={StudyMetadataWrapper} />
        <PrivateRoute path="/my-studies/:catalog/study/:studyId/metadata/details" exact user={user} component={StudyMetadataWrapper} />
        <PrivateRoute path="/my-studies/:catalog/study/:studyId/metadata/centers" exact user={user} component={StudyMetadataWrapper} />
        <PrivateRoute path="/my-studies/:catalog/study/:studyId/metadata/contacts" exact user={user} component={StudyMetadataWrapper} />
        <PrivateRoute path="/my-studies/:catalog/study/:studyId/metadata/consent" exact user={user} component={StudyMetadataWrapper} />
        <PrivateRoute path="/my-studies/:catalog/study/:studyId/metadata/finished" exact user={user} component={StudyMetadataWrapper} />

        /* Admin */
        <ProtectedRoute path="/admin" user={user} component={AdminPageWrapper} />

        <Route component={NotFound} />
    </Switch>;
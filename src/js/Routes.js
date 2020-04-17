import React from "react";
import {Redirect, Route, Switch} from "react-router-dom";
import NotFound from "./pages/NotFound";
import FAIRDataPointMain from "./pages/FAIRDataPoint/FAIRDataPointMain";
import Catalog from "./pages/FAIRDataPoint/Catalog";
import Dataset from "./pages/FAIRDataPoint/Dataset";
import Distribution from "./pages/FAIRDataPoint/Distribution";
import Login from "./pages/Login";
import axios from "axios";
import StudyMetadataWrapper from "./pages/StudyMetadata/StudyMetadataWrapper";
import Catalogs from "./pages/Admin/Catalogs";
import {PrivateRoute, ProtectedRoute} from "./components/Route";
import SingleCatalog from "./pages/Admin/SingleCatalog";
import AddStudy from "./pages/Admin/AddStudy";
import AdminStudyMetadataWrapper from "./pages/Admin/AdminStudyMetadataWrapper";
import MetadataXmlParse from "./pages/Tools/MetadataXmlParse";
import AddDistribution from "./pages/Admin/AddDistribution";

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
        <Route path="/fdp/:catalog" exact component={Catalog} />
        <Route path="/fdp/:catalog/:dataset" exact component={Dataset} />
        <Route path="/fdp/:catalog/:dataset/:distribution" exact component={Distribution} />

        /* Tools */
        <Route path="/tools/metadata-xml-parse" exact component={MetadataXmlParse} />

        /* My studies */
        {/*<PrivateRoute path="/my-studies" exact user={user} component={MyStudies} />*/}
        <PrivateRoute path="/my-studies/:catalog/study/add" exact user={user} component={StudyMetadataWrapper} />
        <PrivateRoute path="/my-studies/:catalog/study/:studyId/metadata/details" exact user={user} component={StudyMetadataWrapper} />
        <PrivateRoute path="/my-studies/:catalog/study/:studyId/metadata/centers" exact user={user} component={StudyMetadataWrapper} />
        <PrivateRoute path="/my-studies/:catalog/study/:studyId/metadata/contacts" exact user={user} component={StudyMetadataWrapper} />
        <PrivateRoute path="/my-studies/:catalog/study/:studyId/metadata/consent" exact user={user} component={StudyMetadataWrapper} />
        <PrivateRoute path="/my-studies/:catalog/study/:studyId/metadata/finished" exact user={user} component={StudyMetadataWrapper} />

        /* Admin */
        <ProtectedRoute path="/admin" exact user={user} component={Catalogs} />
        <ProtectedRoute path="/admin/:catalog" exact user={user} component={SingleCatalog} />
        <ProtectedRoute path="/admin/:catalog/study/add" exact user={user} component={AddStudy} />

        {/*<ProtectedRoute path="/admin/:catalog/study/:studyId" exact user={user} component={SingleCatalog} />*/}
        <ProtectedRoute path="/admin/:catalog/study/:studyId/metadata/:action/details" exact user={user} component={AdminStudyMetadataWrapper} />
        <ProtectedRoute path="/admin/:catalog/study/:studyId/metadata/:action/centers" exact user={user} component={AdminStudyMetadataWrapper} />
        <ProtectedRoute path="/admin/:catalog/study/:studyId/metadata/:action/contacts" exact user={user} component={AdminStudyMetadataWrapper} />
        <ProtectedRoute path="/admin/:catalog/study/:studyId/metadata/:action/consent" exact user={user} component={AdminStudyMetadataWrapper} />

        <ProtectedRoute path="/admin/:catalog/dataset/:dataset/distribution" exact user={user} component={AdminStudyMetadataWrapper} />
        <ProtectedRoute path="/admin/:catalog/dataset/:dataset/distribution/add" exact user={user} component={AddDistribution} />
        <ProtectedRoute path="/admin/:catalog/dataset/:dataset/distribution/:distribution" exact user={user} component={AdminStudyMetadataWrapper} />
        <ProtectedRoute path="/admin/:catalog/dataset/:dataset/distribution/:distribution/content" exact user={user} component={AdminStudyMetadataWrapper} />

        <Route component={NotFound} />
    </Switch>;
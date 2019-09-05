import React from "react";
import {Redirect, Route, Switch} from "react-router-dom";
import NotFound from "./pages/NotFound";
import FAIRDataPoint from "./pages/FAIRDataPoint";
import Catalog from "./pages/Catalog";
import Dataset from "./pages/Dataset";
import Distribution from "./pages/Distribution";
// import Query from "./pages/Query";

export default () =>
    <Switch>
        <Redirect exact from="/" to="/fdp" />
        <Route path="/fdp" exact component={FAIRDataPoint} />
        {/*<Route path="/query" exact component={Query} />*/}
        <Route path="/fdp/:catalogSlug" exact component={Catalog} />
        <Route path="/fdp/:catalogSlug/:datasetSlug" exact component={Dataset} />
        <Route path="/fdp/:catalogSlug/:datasetSlug/:distributionSlug" exact component={Distribution} />
        <Route component={NotFound} />
    </Switch>;
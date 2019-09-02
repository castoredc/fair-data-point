import React from "react";
import {Redirect, Route, Switch} from "react-router-dom";
import NotFound from "./pages/NotFound";
import RDFRender from "./pages/RDFRender";
import Query from "./pages/Query";

export default () =>
    <Switch>
        <Redirect exact from="/" to="/fdp" />
        <Route path="/fdp" exact component={RDFRender} />
        <Route path="/query" exact component={Query} />
        <Route path="/fdp/:catalogSlug" exact component={RDFRender} />
        <Route path="/fdp/:catalogSlug/:datasetSlug" exact component={RDFRender} />
        <Route path="/fdp/:catalogSlug/:datasetSlug/distribution" exact component={RDFRender} />
        <Route component={NotFound} />
    </Switch>;

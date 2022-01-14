import React, {FC} from "react";
import {Redirect, Route, RouteComponentProps, Switch} from "react-router-dom";
import NotFound from "./pages/NotFound";
import Login from "./pages/Login";
import axios from "axios";
import {PrivateRoute} from "components/Route";
import Main from "./pages/Main";
import Wizard from "./pages/Wizard";
import Dashboard from "pages/Dashboard";

axios.interceptors.response.use(function (response) {
    return response;
}, function (error) {
    if (axios.isCancel(error)) {
        return Promise.reject(error);
    } else if (401 === error.response.status) {
        window.location.href = '/login?path=' + encodeURIComponent(window.location.pathname);
    } else {
        return Promise.reject(error);
    }
});

interface RoutesProps {
    user: any,
    embedded: boolean,
}

const Routes: FC<RoutesProps> = ({user, embedded}) => {
    return <Switch>
        <Redirect exact from="/" to="/fdp"/>
        <Route path="/login" exact component={Login}/>
        <Route path="/login/:catalogSlug" exact component={Login}/>

        /* FAIR Data Point */
        <Route path="/fdp" render={(props) => <Main {...props} embedded={embedded} user={user}/>}/>
        <Route path="/study" render={(props) => <Main {...props} embedded={embedded} user={user}/>}/>
        <Route path="/tools" render={(props) => <Main {...props} user={user}/>}/>

        /* Dashboard */
        <PrivateRoute path="/dashboard" user={user} component={Dashboard}/>

        <Route path="/wizard" render={(props) => <Wizard {...props} user={user}/>}/>

        <Route component={NotFound}/>
    </Switch>;
}

export default Routes;
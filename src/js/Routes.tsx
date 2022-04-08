import React, { FC } from "react";
import { Redirect, Route, Switch } from "react-router-dom";
import NotFound from "pages/ErrorPages/NotFound";
import Login from "./pages/Login";
import { PrivateRoute } from "components/Route";
import Main from "./pages/Main";
import Wizard from "./pages/Wizard";
import Dashboard from "pages/Dashboard";
import { UserType } from "./types/UserType";

interface RoutesProps {
  user: UserType | null;
  embedded: boolean;
}

const Routes: FC<RoutesProps> = ({ user, embedded }) => {
  return (
    <Switch>
      <Redirect exact from="/" to="/fdp" />
      <Route path="/login" exact component={Login} />
      <Route path="/login/:catalogSlug" exact component={Login} />
      /* FAIR Data Point */
      <Route
        path="/fdp"
        render={(props) => <Main {...props} embedded={embedded} user={user} />}
      />
      <Route
        path="/study"
        render={(props) => <Main {...props} embedded={embedded} user={user} />}
      />
      <Route
        path="/tools"
        render={(props) => <Main {...props} user={user} />}
      />
      /* Dashboard */
      <PrivateRoute
        path="/dashboard"
        user={user}
        render={(props) => <Dashboard {...props} user={user} />}
      />
      <Route
        path="/wizard"
        render={(props) => <Wizard {...props} user={user} />}
      />
      <Route component={NotFound} />
    </Switch>
  );
};

export default Routes;

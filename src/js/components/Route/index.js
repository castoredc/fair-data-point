import React from "react";
import {Redirect, Route} from 'react-router-dom';

export const PrivateRoute = ({ component: Component, path, user, ...rest }) => (
    <Route {...rest} render={(props) => (
        user !== null
            ? <Component {...props} />
            : <Redirect to={{
                pathname: '/login',
                search:   '?path=' + encodeURI(path)
            }} />
    )} />
);

export const ProtectedRoute = ({ component: Component, path, user, ...rest }) => (
    <Route {...rest} render={(props) => (
        user !== null && user.isAdmin === true
            ? <Component {...props} />
            : <Redirect to={{
                pathname: '/',
            }} />
    )} />
);
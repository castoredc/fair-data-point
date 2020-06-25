import React from "react";
import {Redirect, Route} from 'react-router-dom';

export const PrivateRoute = ({ component: Component, path, user, embedded, ...rest }) => (
    <Route {...rest} render={(props) => (
        user !== null
            ? <Component {...props} user={user} embedded={embedded} />
            : <Redirect to={{
                pathname: '/login',
                search:   '?path=' + encodeURI(window.location.pathname)
            }} />
    )} />
);

export const ProtectedRoute = ({ component: Component, path, user, ...rest }) => (
    <Route {...rest} render={(props) => (
        user !== null && user.isAdmin === true
            ? <Component {...props} user={user} />
            : <Redirect to={{
                pathname: '/',
            }} />
    )} />
);
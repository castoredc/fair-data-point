import React from "react";
import {Redirect, Route} from 'react-router-dom';

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

export default PrivateRoute;
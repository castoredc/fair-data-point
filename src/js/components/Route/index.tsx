import React, {FC} from "react";
import {Redirect, Route, RouteComponentProps, RouteProps} from 'react-router-dom';
import {UserType} from "../../types/UserType";

export interface AuthorizedRouteComponentProps extends RouteComponentProps<any> {
    user: UserType | null;
}

interface CustomRouteProps extends RouteProps {
    user: UserType | null,
    component?: React.ComponentType<AuthorizedRouteComponentProps> | undefined;
}

export const PrivateRoute: FC<CustomRouteProps> = ({component: Component, render, user, ...rest}) => {
    if (user === null) {
        return <Redirect to={{
            pathname: '/login',
            search: '?path=' + encodeURI(window.location.pathname)
        }} />;
    }

    if(Component) {
        return <Route {...rest} render={(props: RouteComponentProps<{}>) => <Component user={user} {...props} />} />
    }

    return <Route {...rest} render={render} />;
};

export const ProtectedRoute: FC<CustomRouteProps> = ({render, user, ...rest}) => {
    if (user !== null && user.isAdmin) {
        return <Route {...rest} render={render} />;
    }

    return <Redirect to={{ pathname: '/?unauthorized=1' }} />;
};
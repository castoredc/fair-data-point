import React, {FC} from "react";
import {Route, RouteComponentProps, Switch} from "react-router-dom";
import './Wizard.scss';
import NotFound from "../NotFound";
import UserDetails from "./UserDetails";
import Affiliations from "./Affiliations";

interface WizardProps extends RouteComponentProps<any> {
    user: any,
}

const Wizard: FC<WizardProps> = ({user, ...rest}) => {
    return <div className="Wizard">
        <Switch>
            <Route path="/wizard/user/details" exact render={(props) => <UserDetails {...rest} user={user}/>}/>
            <Route path="/wizard/user/affiliations" exact render={(props) => <Affiliations {...rest} user={user}/>}/>
            <Route component={NotFound}/>
        </Switch>
    </div>;
};

export default Wizard;
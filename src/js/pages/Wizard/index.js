import React, {Component} from "react";
import {Route, Switch} from "react-router-dom";
import './Wizard.scss';
import NotFound from "../NotFound";
import UserDetails from "./UserDetails";
import Affiliations from "./Affiliations";

export default class Wizard extends Component {
    render() {
        const {user} = this.props;

        return <div className="Wizard">
            <Switch>
                <Route path="/wizard/user/details" exact render={(props) => <UserDetails {...props} user={user}/>}/>
                <Route path="/wizard/user/affiliations" exact render={(props) => <Affiliations {...props} user={user}/>}/>

                <Route component={NotFound}/>
            </Switch>
        </div>;
    }
}

import React, {Component} from "react";
import {Route, Switch} from "react-router-dom";
import './Wizard.scss';
import NotFound from "../NotFound";
import Onboarding from "./Onboarding";

export default class Wizard extends Component {
    render() {
        const {user} = this.props;

        return <div className="Wizard">
            <Switch>
                <Route path="/wizard/onboarding" exact render={(props) => <Onboarding {...props} user={user}/>}/>

                <Route component={NotFound}/>
            </Switch>
        </div>;
    }
}

import React, {FC} from "react";
import {Route, RouteComponentProps, Switch} from "react-router-dom";
import './Wizard.scss';
import NotFound from "../NotFound";
import UserDetails from "./UserDetails";
import Affiliations from "./Affiliations";
import {CastorBar} from "@castoredc/matter";

interface WizardProps extends RouteComponentProps<any> {
    user: any,
}

const Wizard: FC<WizardProps> = ({user, ...rest}) => {
    return <div className="Wizard">
        <CastorBar
            items={[
                {
                    destination: () => '/',
                    label: 'Castor',
                    type: 'brand',
                },
                {
                    items: [
                        {
                            isTitle: true,
                            label: 'Account',
                        },
                        {
                            destination: '/logout',
                            icon: 'logOut',
                            label: 'Log out',
                        },
                    ],
                    label: 'Account',
                    type: 'account',
                },
            ]}
            label="Castor navigation"
            horizontalNav
        />
        <Switch>
            <Route path="/wizard/user/details" exact render={(props) => <UserDetails {...rest} user={user}/>}/>
            <Route path="/wizard/user/affiliations" exact render={(props) => <Affiliations {...rest} user={user}/>}/>
            <Route component={NotFound}/>
        </Switch>
    </div>;
};

export default Wizard;
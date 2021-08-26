import React, {Component, FunctionComponent} from "react";
import {Redirect, Route, RouteComponentProps, Switch} from "react-router-dom";
import NotFound from "../NotFound";
import {CastorBar, Stack, StackItem, Tabs} from "@castoredc/matter";
import Studies from "./Studies";
import './Dashboard.scss';
import {toRem} from '@castoredc/matter-utils';
import AddStudy from "pages/Dashboard/Studies/AddStudy";
import DataModels from "pages/Dashboard/DataModels";
import Study from "pages/Dashboard/Studies/Study";
import Dataset from "pages/Dashboard/Studies/Study/Dataset";
import Distribution from "pages/Dashboard/Studies/Study/Dataset/Distribution";
import Catalogs from "pages/Dashboard/Catalogs";

interface DashboardProps extends RouteComponentProps<any> {
}

const DashboardTabs: FunctionComponent<RouteComponentProps> = ({history, location, match}) => {
    const urls = {
        '/dashboard/studies': 'studies',
        '/dashboard/data-models': 'dataModels',
    };

    const tabs = {
        studies: {
            content: <Studies history={history} location={location} match={match}/>,
            title: 'Studies',
        },
        catalogs: {
            content: <Catalogs history={history} location={location} match={match}/>,
            title: 'Catalogs',
        },
        dataModels: {
            content: <DataModels history={history} location={location} match={match}/>,
            title: 'Data models',
        },
    };

    return <div style={{marginLeft: 'auto', marginRight: 'auto'}}>
        <Stack distribution="center">
            <StackItem style={{width: toRem(960), marginTop: '3.2rem'}}>
                <Tabs
                    tabs={tabs}
                    selected={urls[location.pathname]}
                    onChange={(selectedKey) => {
                        const newUrl = Object.keys(urls).find(key => urls[key] === selectedKey) ?? '/dashboard/studies';
                        history.push(newUrl);
                    }}
                />
            </StackItem>
        </Stack>
    </div>
}

export default class Dashboard extends Component<DashboardProps> {
    constructor(props) {
        super(props);
    };

    render() {
        const {history} = this.props;

        return <div className="Dashboard" style={{paddingTop: '4.8rem'}}>
            <CastorBar
                items={[
                    {
                        destination: () => history.push('/dashboard/studies'),
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
            <div className="Main">
                <Switch>
                    <Redirect exact from="/dashboard" to="/dashboard/studies"/>

                    <Route path="/dashboard/studies" exact component={DashboardTabs}/>

                    <Route path="/dashboard/studies/add" exact component={AddStudy}/>
                    <Route path="/dashboard/studies/add/:catalog" exact component={AddStudy}/>

                    <Route path="/dashboard/studies/:study/datasets/:dataset/distributions/:distribution" component={Distribution}/>
                    <Route path="/dashboard/studies/:study/datasets/:dataset" component={Dataset}/>
                    <Route path="/dashboard/studies/:study" component={Study}/>

                    <Route path="/dashboard/data-models" exact component={DashboardTabs}/>

                    <Route component={NotFound}/>
                </Switch>
            </div>
        </div>;
    }
}

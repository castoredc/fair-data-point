import React, {Component, createRef} from "react";
import {matchPath, Redirect, Route, Switch} from "react-router-dom";
import './Admin.scss';
import NotFound from "../NotFound";
import Study from "./Study";
import DataModels from "./Home/DataModels";
import DataModel from "./DataModel";
import Catalogs from "./Home/Catalogs";
import {CastorBar} from "@castoredc/matter";
import Studies from "./Home/Studies";
import Catalog from "./Catalog";
import Distribution from "./Distribution";
import Dataset from "./Dataset";
import Datasets from "./Home/Datasets";
import CustomIcon from "../../components/Icon/CustomIcon";
import FAIRDataPoint from "./FAIRDataPoint";
import DataDictionaries from "./Home/DataDictionaries";
import DataDictionary from "./DataDictionary";

export default class Admin extends Component {
    constructor(props) {
        super(props);

        this.state = {
            showMenu: false,
        };

        this.link = createRef();
        this.menu = createRef();
    };

    toggleMenu = () => {
        const {showMenu} = this.state;

        this.setState({
            showMenu: !showMenu,
        });
    };

    render() {
        const {history} = this.props;

        return <div className="Admin">
            <CastorBar
                items={[
                    {
                        destination: '#',
                        label: 'Castor',
                        type: 'brand',
                    },
                    {
                        destination: () => history.push('/admin/fdp/metadata'),
                        icon: <CustomIcon type="fair"/>,
                        label: 'FAIR Data Point',
                        isCurrent: matchPath(window.location.pathname, {
                            path: "/admin/fdp/metadata",
                            exact: true,
                            strict: false,
                        }),
                    },
                    {
                        destination: () => history.push('/admin/catalogs'),
                        icon: <CustomIcon type="catalog"/>,
                        label: 'Catalogs',
                        isCurrent: (
                            matchPath(window.location.pathname, {
                                path: "/admin/catalogs",
                                exact: true,
                                strict: false,
                            }) || matchPath(window.location.pathname, {
                                path: "/admin/catalog/:catalog",
                                exact: false,
                                strict: false,
                            })
                        ),
                    },
                    {
                        destination: () => history.push('/admin/datasets'),
                        icon: <CustomIcon type="dataset"/>,
                        label: 'Datasets',
                        isCurrent: (
                            matchPath(window.location.pathname, {
                                path: "/admin/datasets",
                                exact: true,
                                strict: false,
                            }) || matchPath(window.location.pathname, {
                                path: "/admin/dataset/:dataset",
                                exact: false,
                                strict: false,
                            })
                        ),
                    },
                    {
                        destination: () => history.push('/admin/studies'),
                        icon: 'study',
                        label: 'Studies',
                        isCurrent: (
                            matchPath(window.location.pathname, {
                                path: "/admin/studies",
                                exact: true,
                                strict: false,
                            }) || matchPath(window.location.pathname, {
                                path: "/admin/study/:study",
                                exact: false,
                                strict: false,
                            })
                        ),
                    },
                    {
                        destination: () => history.push('/admin/models'),
                        icon: 'structure',
                        label: 'Data models',
                        isCurrent: (
                            matchPath(window.location.pathname, {
                                path: "/admin/models",
                                exact: true,
                                strict: false,
                            }) || matchPath(window.location.pathname, {
                                path: "/admin/model/:model",
                                exact: false,
                                strict: false,
                            })
                        ),
                    },
                    {
                        destination: () => history.push('/admin/dictionaries'),
                        icon: 'summary',
                        label: 'Data dictionaries',
                        isCurrent: (
                            matchPath(window.location.pathname, {
                                path: "/admin/dictionaries",
                                exact: true,
                                strict: false,
                            }) || matchPath(window.location.pathname, {
                                path: "/admin/dictionary/:dictionary",
                                exact: false,
                                strict: false,
                            })
                        ),
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
            />
            <div className="Main">
                <Switch>
                    <Redirect exact from="/admin" to="/admin/catalogs"/>

                    <Route path="/admin/fdp" component={FAIRDataPoint}/>

                    <Route path="/admin/catalogs" exact component={Catalogs}/>
                    <Route path="/admin/catalog/:catalog" component={Catalog}/>

                    <Route path="/admin/datasets" component={Datasets}/>
                    <Route path="/admin/dataset/:dataset/distribution/:distribution" component={Distribution}/>
                    <Route path="/admin/dataset/:dataset" component={Dataset}/>

                    <Route path="/admin/studies" exact component={Studies}/>
                    <Route path="/admin/study/:study" component={Study}/>

                    <Route path="/admin/models" exact component={DataModels}/>
                    <Route path="/admin/model/:model/:version" component={DataModel}/>
                    <Route path="/admin/model/:model" component={DataModel}/>

                    <Route path="/admin/dictionaries" exact component={DataDictionaries}/>
                    <Route path="/admin/dictionary/:dictionary" component={DataDictionary}/>

                    <Route component={NotFound}/>
                </Switch>
            </div>
        </div>;
    }
}

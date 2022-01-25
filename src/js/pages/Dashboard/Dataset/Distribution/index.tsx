import React, {Component} from "react";
import axios from "axios";
import {toast} from "react-toastify";
import ToastContent from "components/ToastContent";
import {Banner, Button, LoadingOverlay} from "@castoredc/matter";
import {Route, Switch} from 'react-router-dom';
import DocumentTitle from "components/DocumentTitle";
import {localizedText} from "../../../../util";
import Header from "components/Layout/Dashboard/Header";
import Body from "components/Layout/Dashboard/Body";
import SideBar from "components/SideBar";
import NotFound from "pages/ErrorPages/NotFound";
import DistributionMetadata from "pages/Dashboard/Dataset/Distribution/DistributionMetadata";
import DistributionSubset from "pages/Dashboard/Dataset/Distribution/DistributionSubset";
import DistributionLog from "pages/Dashboard/Dataset/Distribution/DistributionLog";
import DistributionLogs from "pages/Dashboard/Dataset/Distribution/DistributionLogs";
import Details from "pages/Dashboard/Dataset/Distribution/Details";
import DistributionContentsCsv from "pages/Dashboard/Dataset/Distribution/DistributionContentsCsv";
import DistributionContentsRdf from "pages/Dashboard/Dataset/Distribution/DistributionContentsRdf";
import {AuthorizedRouteComponentProps} from "components/Route";
import {isGranted} from "utils/PermissionHelper";
import PermissionEditor from "components/PermissionEditor";
import NoPermission from "pages/ErrorPages/NoPermission";

interface DistributionProps extends AuthorizedRouteComponentProps {
    dataset: any,
}

interface DistributionState {
    distribution: any,
    contents: any,
    isLoading: boolean,
}

export default class Distribution extends Component<DistributionProps, DistributionState> {
    constructor(props) {
        super(props);

        this.state = {
            distribution: null,
            contents: null,
            isLoading: true,
        };
    }

    getDistribution = () => {
        this.setState({
            isLoading: true,
        });

        const {match} = this.props;

        axios.get('/api/dataset/' + match.params.dataset + '/distribution/' + match.params.distribution)
            .then((response) => {
                this.setState({
                    distribution: response.data,
                }, this.getContents);
            })
            .catch((error) => {
                this.setState({
                    isLoading: false,
                });

                if (error.response && typeof error.response.data.error !== "undefined") {
                    toast.error(<ToastContent type="error" message={error.response.data.error}/>);
                } else {
                    toast.error(<ToastContent type="error"
                                              message="An error occurred while loading your distribution"/>);
                }
            });
    };

    getContents = () => {
        const {match} = this.props;

        axios.get('/api/dataset/' + match.params.dataset + '/distribution/' + match.params.distribution + '/contents')

            .then((response) => {
                this.setState({
                    contents: response.data.elements,
                    isLoading: false,
                });
            })
            .catch((error) => {
                this.setState({
                    isLoading: false,
                });

                const message = (error.response && typeof error.response.data.error !== "undefined") ? error.response.data.error : 'An error occurred while loading the distribution';
                toast.error(<ToastContent type="error" message={message}/>);
            });
    };

    componentDidMount() {
        this.getDistribution();
    }

    render() {
        const {history, location, match, user} = this.props;
        const {isLoading, distribution, contents} = this.state;

        if (isLoading) {
            return <LoadingOverlay accessibleLabel="Loading distribution"/>;
        }

        if(! isGranted('edit', distribution.permissions)) {
            return <NoPermission text="You do not have permission to edit this distribution"/>;
        }

        const catalog = match.params.catalog;
        const study = match.params.study;
        const dataset = match.params.dataset;

        const title = distribution.hasMetadata ? localizedText(distribution.metadata.title, 'en') : 'Untitled distribution';
        
        const mainUrl = match.params.study ? '/dashboard/studies/' + match.params.study + '/datasets/' + dataset : '/dashboard/catalogs/' + match.params.catalog + '/datasets/' + dataset;

        let sidebarItems = [
            {
                to: mainUrl + '/distributions/' + distribution.slug,
                exact: true,
                title: 'Distribution',
                customIcon: 'distribution'
            },
            {
                to: mainUrl + '/distributions/' + distribution.slug + '/metadata',
                exact: true,
                title: 'Metadata',
                customIcon: 'metadata'
            },
            ...isGranted('manage', distribution.permissions) ? [{
                to: mainUrl + '/distributions/' + distribution.slug + '/permissions',
                exact: true,
                title: 'Permissions',
                icon: 'usersLight'
            }] : [],
            {
                to: mainUrl + '/distributions/' + distribution.slug + '/subset',
                exact: true,
                title: 'Subset',
                icon: 'selectList'
            },
            {
                type: 'separator'
            }
        ];

        if (distribution.type === 'rdf') {
            sidebarItems.push(
                {
                    to: mainUrl + '/distributions/' + distribution.slug + '/contents',
                    exact: true,
                    title: 'Mappings',
                    icon: 'order'
                }
            );

            if (distribution.isCached) {
                sidebarItems.push(
                    {
                        to: mainUrl + '/distributions/' + distribution.slug + '/log',
                        exact: true,
                        title: 'Log',
                        icon: 'summary'
                    }
                );
            }
        } else if (distribution.type === 'csv') {
            sidebarItems.push(
                {
                    to: mainUrl + '/distributions/' + distribution.slug + '/contents',
                    exact: true,
                    title: 'Contents',
                    icon: 'order'
                }
            )
        }

        return <>
            <DocumentTitle title={title}/>

            <SideBar
                back={{
                    to: mainUrl,
                    title: 'Back to dataset'
                }}
                location={location}
                items={sidebarItems}
            />

            <Body>
                <Header title={title}>
                    <Button
                        buttonType="contentOnly"
                        icon="openNewWindow"
                        href={`/fdp/dataset/${dataset}/distribution/${distribution.slug}`}
                        target="_blank"
                    >
                        View
                    </Button>
                </Header>

                <Switch>
                    <Route
                        path={[
                            "/dashboard/studies/:study/datasets/:dataset/distributions/:distribution",
                            "/dashboard/catalogs/:catalog/datasets/:dataset/distributions/:distribution"
                        ]}
                        exact
                        render={(props) => (
                            <Details
                                {...props}
                                user={user}
                                catalog={catalog}
                                study={study}
                                dataset={dataset}
                                distribution={distribution}
                            />
                        )}
                    />

                    <Route
                        path={[
                            "/dashboard/studies/:study/datasets/:dataset/distributions/:distribution/metadata",
                            "/dashboard/catalogs/:catalog/datasets/:dataset/distributions/:distribution/metadata"
                        ]}
                        exact
                        render={(props) => (
                            <DistributionMetadata
                                {...props}
                                dataset={dataset}
                                distribution={distribution}
                                onSave={this.getDistribution}/>
                        )}
                    />

                    <Route
                        path={[
                            "/dashboard/studies/:study/datasets/:dataset/distributions/:distribution/permissions",
                            "/dashboard/catalogs/:catalog/datasets/:dataset/distributions/:distribution/permissions"
                        ]}
                        exact
                           render={(props) => isGranted('manage', distribution.permissions) ?
                               <PermissionEditor
                                   getObject={this.getDistribution}
                                   type="distribution"
                                   object={distribution}
                                   user={user}
                                   {...props}
                               /> : <NoPermission text="You do not have access to this page"/>
                           }
                    />

                    <Route
                        path={[
                            "/dashboard/studies/:study/datasets/:dataset/distributions/:distribution/contents",
                            "/dashboard/catalogs/:catalog/datasets/:dataset/distributions/:distribution/contents"
                        ]}
                        exact
                        render={(props) => {
                            if (distribution.type === 'csv') {
                                return <DistributionContentsCsv contents={contents} catalog={catalog}
                                                                distribution={distribution} dataset={dataset}/>;
                            } else if (distribution.type === 'rdf') {
                                return <DistributionContentsRdf dataset={dataset} distribution={distribution}/>;
                            }

                            return <div className="NoResults">This distribution does not have contents.</div>;
                        }}
                    />

                    <Route
                        path={[
                            "/dashboard/studies/:study/datasets/:dataset/distributions/:distribution/log/:log",
                            "/dashboard/catalogs/:catalog/datasets/:dataset/distributions/:distribution/log/:log"
                        ]}
                        exact
                        render={(props) => (
                            <DistributionLog
                                {...props}
                                dataset={dataset}
                                distribution={distribution}
                            />
                        )}
                    />

                    <Route
                        path={[
                            "/dashboard/studies/:study/datasets/:dataset/distributions/:distribution/log",
                            "/dashboard/catalogs/:catalog/datasets/:dataset/distributions/:distribution/log"
                        ]}
                        exact
                        render={(props) => (
                            <DistributionLogs
                                {...props}
                                dataset={dataset}
                                distribution={distribution}
                            />
                        )}
                    />

                    <Route
                        path={[
                            "/dashboard/studies/:study/datasets/:dataset/distributions/:distribution/subset",
                            "/dashboard/catalogs/:catalog/datasets/:dataset/distributions/:distribution/subset"
                        ]}
                        exact
                        render={(props) => (
                            <DistributionSubset
                                {...props}
                                dataset={dataset}
                                distribution={distribution}
                            />
                        )}
                    />

                    <Route component={NotFound}/>
                </Switch>
            </Body>
        </>;
    }
}

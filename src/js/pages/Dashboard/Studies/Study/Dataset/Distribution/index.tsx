import React, {Component} from "react";
import axios from "axios";
import {toast} from "react-toastify";
import ToastContent from "components/ToastContent";
import {LoadingOverlay} from "@castoredc/matter";
import {Route, RouteComponentProps, Switch} from 'react-router-dom';
import DocumentTitle from "components/DocumentTitle";
import {localizedText} from "../../../../../../util";
import Header from "components/Layout/Dashboard/Header";
import Body from "components/Layout/Dashboard/Body";
import SideBar from "components/SideBar";
import NotFound from "pages/NotFound";
import DistributionDetails from "pages/Dashboard/Studies/Study/Dataset/Distribution/DistributionDetails";
import DistributionMetadata from "pages/Dashboard/Studies/Study/Dataset/Distribution/DistributionMetadata";
import DistributionContents from "pages/Dashboard/Studies/Study/Dataset/Distribution/DistributionContents";
import DistributionSubset from "pages/Dashboard/Studies/Study/Dataset/Distribution/DistributionSubset";
import DistributionLog from "pages/Dashboard/Studies/Study/Dataset/Distribution/DistributionLog";
import DistributionLogs from "pages/Dashboard/Studies/Study/Dataset/Distribution/DistributionLogs";

interface DistributionProps extends RouteComponentProps<any> {
    dataset: any,
}

interface DistributionState {
    distribution: any,
    isLoading: boolean,
}

export default class Distribution extends Component<DistributionProps, DistributionState> {
    constructor(props) {
        super(props);

        this.state = {
            distribution: null,
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
                    isLoading: false,
                });
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

    componentDidMount() {
        this.getDistribution();
    }

    render() {
        const {history, location, match} = this.props;
        const {isLoading, distribution} = this.state;

        if (isLoading) {
            return <LoadingOverlay accessibleLabel="Loading distribution"/>;
        }

        const study = match.params.study;
        const dataset = match.params.dataset;

        const title = distribution.hasMetadata ? localizedText(distribution.metadata.title, 'en') : 'Untitled distribution';

        let sidebarItems = [
            {
                to: '/dashboard/studies/' + study + '/datasets/' + dataset + '/distributions/' + distribution.slug,
                exact: true,
                title: 'Distribution',
                customIcon: 'distribution'
            },
            {
                to: '/dashboard/studies/' + study + '/datasets/' + dataset + '/distributions/' + distribution.slug + '/metadata',
                exact: true,
                title: 'Metadata',
                customIcon: 'metadata'
            },
            {
                to: '/dashboard/studies/' + study + '/datasets/' + dataset + '/distributions/' + distribution.slug + '/subset',
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
                    to: '/dashboard/studies/' + study + '/datasets/' + dataset + '/distributions/' + distribution.slug + '/contents',
                    exact: true,
                    title: 'Mappings',
                    icon: 'order'
                }
            );

            if (distribution.isCached) {
                sidebarItems.push(
                    {
                        to: '/dashboard/studies/' + study + '/datasets/' + dataset + '/distributions/' + distribution.slug + '/log',
                        exact: true,
                        title: 'Log',
                        icon: 'summary'
                    }
                );
            }
        } else if (distribution.type === 'csv') {
            sidebarItems.push(
                {
                    to: '/dashboard/studies/' + study + '/datasets/' + dataset + '/distributions/' + distribution.slug + '/contents',
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
                    to: `/dashboard/studies/${study}/datasets/${dataset}`,
                    title: 'Back to dataset'
                }}
                location={location}
                items={sidebarItems}
            />

            <Body>
                <Header title={title}/>

                <Switch>
                    <Route path="/dashboard/studies/:study/datasets/:dataset/distributions/:distribution" exact
                           render={(props) => <DistributionDetails {...props}
                                                                   dataset={dataset}
                                                                   distribution={distribution}/>}/>
                    <Route path="/dashboard/studies/:study/datasets/:dataset/distributions/:distribution/metadata" exact
                           render={(props) => <DistributionMetadata {...props}
                                                                    dataset={dataset}
                                                                    distribution={distribution}
                                                                    onSave={this.getDistribution}/>}/>
                    <Route path="/dashboard/studies/:study/datasets/:dataset/distributions/:distribution/contents" exact
                           render={(props) => <DistributionContents {...props}
                                                                    dataset={dataset} distribution={distribution}/>}/>
                    <Route path="/dashboard/studies/:study/datasets/:dataset/distributions/:distribution/log/:log" exact
                           render={(props) => <DistributionLog {...props}
                                                               dataset={dataset}
                                                               distribution={distribution}/>}/>
                    <Route path="/dashboard/studies/:study/datasets/:dataset/distributions/:distribution/log" exact
                           render={(props) => (
                               <DistributionLogs {...props}
                                                 dataset={dataset}
                                                 distribution={distribution}
                                                 study={study}
                               />
                           )}
                    />
                    <Route path="/dashboard/studies/:study/datasets/:dataset/distributions/:distribution/subset" exact
                           render={(props) => <DistributionSubset {...props}
                                                                  dataset={dataset}
                                                                  distribution={distribution}/>}/>
                    <Route component={NotFound}/>
                </Switch>
            </Body>
        </>;
    }
}

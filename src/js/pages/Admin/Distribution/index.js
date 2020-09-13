import React, {Component} from "react";
import axios from "axios";
import {localizedText} from "../../../util";
import {toast} from "react-toastify";
import ToastContent from "../../../components/ToastContent";
import InlineLoader from "../../../components/LoadingScreen/InlineLoader";
import NotFound from "../../NotFound";
import {Route, Switch} from "react-router-dom";
import DistributionDetails from "./DistributionDetails";
import DistributionContents from "./DistributionContents";
import DistributionMetadata from "./DistributionMetadata";
import {ViewHeader} from "@castoredc/matter";
import DistributionLogs from "./DistributionLogs";
import DistributionLog from "./DistributionLog";
import DocumentTitle from "../../../components/DocumentTitle";
import SideBar from "../../../components/SideBar";

export default class Distribution extends Component {
    constructor(props) {
        super(props);
        this.state = {
            isLoadingDistribution: true,
            hasLoadedDistribution: false,
            distribution:          null,
            dataset:               props.match.params.dataset,
        };
    }

    componentDidMount() {
        this.getDistribution();
    }

    getDistribution = () => {
        this.setState({
            isLoadingDistribution: true,
        });

        axios.get('/api/dataset/' + this.props.match.params.dataset + '/distribution/' + this.props.match.params.distribution)
            .then((response) => {
                this.setState({
                    distribution:          response.data,
                    isLoadingDistribution: false,
                    hasLoadedDistribution: true,
                });
            })
            .catch((error) => {
                this.setState({
                    isLoadingDistribution: false,
                });

                const message = (error.response && typeof error.response.data.error !== "undefined") ? error.response.data.error : 'An error occurred while loading the distribution';
                toast.error(<ToastContent type="error" message={message}/>);
            });
    };

    render() {
        const {distribution, isLoadingDistribution, dataset} = this.state;
        const {location} = this.props;

        if (isLoadingDistribution) {
            return <InlineLoader/>;
        }

        const title = distribution.hasMetadata ? localizedText(distribution.metadata.title, 'en') : null;

        let sidebarItems = [
            {
                to: '/admin/dataset/' + dataset + '/distribution/' + distribution.slug,
                exact: true,
                title: 'Distribution',
                customIcon: 'distribution'
            },
            {
                to: '/admin/dataset/' + dataset + '/distribution/' + distribution.slug + '/metadata',
                exact: true,
                title: 'Metadata',
                customIcon: 'metadata'
            },
            {
                type: 'separator'
            }
        ];

        if(distribution.type === 'rdf') {
            sidebarItems.push(
                {
                    to: '/admin/dataset/' + dataset + '/distribution/' + distribution.slug + '/contents',
                    exact: true,
                    title: 'Mappings'
                },
                {
                    to: '/admin/dataset/' + dataset + '/distribution/' + distribution.slug + '/log',
                    exact: true,
                    title: 'Log',
                }
            );
        } else if(distribution.type === 'csv') {
            sidebarItems.push(
                {
                    to: '/admin/dataset/' + dataset + '/distribution/' + distribution.slug + '/contents',
                    exact: true,
                    title: 'Contents'
                }
            )
        }

        return <div className="PageContainer">
            <DocumentTitle title={'FDP Admin | Distribution' + (title ? ` | ${title}` : '')}/>

            <SideBar
                back={{
                    to: '/admin/dataset/' + dataset,
                    title: 'Back to dataset'
                }}
                location={location}
                items={sidebarItems}
            />

            <div className="Page">
                <div className="PageTitle">
                    <ViewHeader>{title ? title : 'Distribution'}</ViewHeader>
                </div>

                <Switch>
                    <Route path="/admin/dataset/:dataset/distribution/:distribution" exact
                           render={(props) => <DistributionDetails {...props} dataset={dataset}
                                                                   distribution={distribution}/>}/>
                    <Route path="/admin/dataset/:dataset/distribution/:distribution/metadata" exact
                           render={(props) => <DistributionMetadata {...props} dataset={dataset}
                                                                    distribution={distribution}
                                                                    onSave={this.getDistribution}/>}/>
                    <Route path="/admin/dataset/:dataset/distribution/:distribution/contents" exact
                           render={(props) => <DistributionContents {...props} dataset={dataset}
                                                                    distribution={distribution}/>}/>
                    <Route path="/admin/dataset/:dataset/distribution/:distribution/log/:log" exact
                           render={(props) => <DistributionLog {...props} dataset={dataset}
                                                               distribution={distribution}/>}/>
                    <Route path="/admin/dataset/:dataset/distribution/:distribution/log" exact
                           render={(props) => <DistributionLogs {...props} dataset={dataset}
                                                                distribution={distribution}/>}/>
                    <Route component={NotFound}/>
                </Switch>
            </div>
        </div>;
    }
}
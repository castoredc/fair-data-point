import React, {Component} from "react";
import axios from "axios";
import {localizedText} from "../../../util";
import InlineLoader from "../../../components/LoadingScreen/InlineLoader";
import NotFound from "../../NotFound";
import {Route, Switch} from "react-router-dom";
import {toast} from "react-toastify";
import ToastContent from "../../../components/ToastContent";
import DatasetDistributions from "../Dataset/DatasetDistributions";
import AddDistribution from "../Dataset/AddDistribution";
import DatasetMetadata from "./DatasetMetadata";
import DatasetDetails from "./DatasetDetails";
import {ViewHeader} from "@castoredc/matter";
import DocumentTitle from "../../../components/DocumentTitle";
import SideBar from "../../../components/SideBar";

export default class Dataset extends Component {
    constructor(props) {
        super(props);
        this.state = {
            isLoadingDataset: true,
            dataset:          null,
            catalog:          props.match.params.catalog,
        };
    }

    componentDidMount() {
        this.getDataset();
    }

    getDataset = () => {
        axios.get('/api/dataset/' + this.props.match.params.dataset)
            .then((response) => {
                this.setState({
                    dataset:          response.data,
                    isLoadingDataset: false,
                });
            })
            .catch((error) => {
                this.setState({
                    isLoadingDataset: false,
                });

                const message = (error.response && typeof error.response.data.error !== "undefined") ? error.response.data.error : 'An error occurred while loading the dataset';
                toast.error(<ToastContent type="error" message={message}/>);
            });
    };

    render() {
        const {dataset, isLoadingDataset, catalog} = this.state;
        const {location} = this.props;

        if (isLoadingDataset) {
            return <InlineLoader/>;
        }

        const url = '/admin' + (catalog ? '/catalog/' + catalog : '');

        const title = dataset.hasMetadata ? localizedText(dataset.metadata.title, 'en') : null;

        return <div className="PageContainer">
            <DocumentTitle title={'FDP Admin | Dataset' + (title ? ` | ${title}` : '')}/>

            <SideBar
                back={catalog ? {
                    to: url + '/studies',
                    title: 'Back to catalog'
                } : undefined}
                location={location}
                items={[
                    {
                        to: url + '/dataset/' + dataset.slug,
                        exact: true,
                        title: 'Dataset',
                        customIcon: 'dataset'
                    },
                    {
                        to: url + '/dataset/' + dataset.slug + '/metadata',
                        exact: true,
                        title: 'Metadata',
                        customIcon: 'metadata'
                    },
                    {
                        type: 'separator'
                    },
                    {
                        to: url + '/dataset/' + dataset.slug + '/distributions',
                        exact: true,
                        title: 'Distributions',
                        customIcon: 'distribution'
                    }
                ]}
            />

            <div className="Page">
                <div className="PageTitle">
                    <ViewHeader>{title ? title : 'Dataset'}</ViewHeader>
                </div>

                <Switch>
                    <Route path="/admin/catalog/:catalog/dataset/:dataset" exact
                           render={(props) => <DatasetDetails {...props} catalog={catalog} dataset={dataset}
                                                              onSave={this.getDataset}/>}/>
                    <Route path="/admin/catalog/:catalog/dataset/:dataset/metadata" exact
                           render={(props) => <DatasetMetadata {...props} catalog={catalog} dataset={dataset}
                                                               onSave={this.getDataset}/>}/>
                    <Route path="/admin/catalog/:catalog/dataset/:dataset/distributions" exact
                           render={(props) => <DatasetDistributions {...props} catalog={catalog} dataset={dataset}/>}/>
                    <Route path="/admin/catalog/:catalog/dataset/:dataset/distributions/add" exact
                           render={(props) => <AddDistribution {...props} catalog={catalog} dataset={dataset}/>}/>

                    <Route path="/admin/dataset/:dataset" exact
                           render={(props) => <DatasetDetails {...props} dataset={dataset} onSave={this.getDataset}/>}/>
                    <Route path="/admin/dataset/:dataset/metadata" exact
                           render={(props) => <DatasetMetadata {...props} dataset={dataset}
                                                               onSave={this.getDataset}/>}/>
                    <Route path="/admin/dataset/:dataset/distributions" exact
                           render={(props) => <DatasetDistributions {...props} dataset={dataset}/>}/>
                    <Route path="/admin/dataset/:dataset/distributions/add" exact
                           render={(props) => <AddDistribution {...props} dataset={dataset}/>}/>


                    <Route component={NotFound}/>
                </Switch>
            </div>
        </div>;
    }
}
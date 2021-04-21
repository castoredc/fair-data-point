import React, {Component} from "react";
import axios from "axios";
import {localizedText} from "../../../util";
import {toast} from "react-toastify";
import ToastContent from "../../../components/ToastContent";
import Layout from "../../../components/Layout";
import Header from "../../../components/Layout/Header";
import MainBody from "../../../components/Layout/MainBody";
import {getBreadCrumbs} from "../../../utils/BreadcrumbUtils";
import MetadataSideBar from "../../../components/MetadataSideBar";
import DistributionList from "../../../components/List/DistributionList";
import AssociatedItemsBar from "../../../components/AssociatedItemsBar";

export default class Dataset extends Component {
    constructor(props) {
        super(props);
        this.state = {
            isLoadingDataset: true,
            hasLoadedDataset: false,
            dataset: null,
        };
    }

    componentDidMount() {
        this.getDataset();
    }

    getDataset = () => {
        axios.get('/api/dataset/' + this.props.match.params.dataset)
            .then((response) => {
                this.setState({
                    dataset: response.data,
                    isLoadingDataset: false,
                    hasLoadedDataset: true
                });
            })
            .catch((error) => {
                this.setState({
                    isLoadingDataset: false
                });

                const message = (error.response && typeof error.response.data.error !== "undefined") ? error.response.data.error : 'An error occurred while loading the dataset';
                toast.error(<ToastContent type="error" message={message}/>);
            });
    };

    render() {
        const {dataset, distributions, isLoadingDataset, isLoadingDistributions} = this.state;
        const {location, user, embedded} = this.props;

        const breadcrumbs = getBreadCrumbs(location, {dataset});

        const title = dataset ? localizedText(dataset.metadata.title, 'en') : null;

        return <Layout
            className="Dataset"
            title={title}
            isLoading={isLoadingDataset}
            embedded={embedded}
        >
            <Header user={user} embedded={embedded} breadcrumbs={breadcrumbs} title={title}/>

            <MainBody isLoading={isLoadingDataset}>
                {dataset && <>
                    <div className="MainCol">
                        {dataset.metadata.description && <div
                            className="InformationDescription">{localizedText(dataset.metadata.description, 'en', true)}</div>}
                    </div>

                    <div className="SideCol">
                        <MetadataSideBar type="dataset" metadata={dataset.metadata} name={title} />
                    </div>

                    <AssociatedItemsBar items={dataset.count} current="distribution" />

                    <DistributionList dataset={dataset} embedded={embedded} className="MainCol" />
                </>}
            </MainBody>
        </Layout>;
    }
}

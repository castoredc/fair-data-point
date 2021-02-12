import React, {Component} from "react";
import axios from "axios";
import {localizedText} from "../../../util";
import ListItem from "../../../components/ListItem";
import {toast} from "react-toastify";
import ToastContent from "../../../components/ToastContent";
import InlineLoader from "../../../components/LoadingScreen/InlineLoader";
import Layout from "../../../components/Layout";
import Header from "../../../components/Layout/Header";
import MainBody from "../../../components/Layout/MainBody";
import {Heading} from "@castoredc/matter";
import {getBreadCrumbs} from "../../../utils/BreadcrumbUtils";
import Publishers from "../../../components/MetadataItem/Publishers";
import MetadataItem from "../../../components/MetadataItem";
import Language from "../../../components/MetadataItem/Language";
import License from "../../../components/MetadataItem/License";
import MetadataSideBar from "../../../components/MetadataSideBar";

export default class Dataset extends Component {
    constructor(props) {
        super(props);
        this.state = {
            isLoadingDataset: true,
            hasLoadedDataset: false,
            isLoadingDistributions: true,
            hasLoadedDistributions: false,
            dataset: null,
            distributions: []
        };
    }

    componentDidMount() {
        this.getDataset();
        this.getDistributions();
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

    getDistributions = () => {
        axios.get('/api/dataset/' + this.props.match.params.dataset + '/distribution')
            .then((response) => {
                this.setState({
                    distributions: response.data.filter((distribution) => {
                        return distribution.hasMetadata
                    }),
                    isLoadingDistributions: false,
                    hasLoadedDistributions: true
                });
            })
            .catch((error) => {
                this.setState({
                    isLoadingDistributions: false
                });

                const message = (error.response && typeof error.response.data.error !== "undefined") ? error.response.data.error : 'An error occurred while loading the distributions';
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

                        {isLoadingDistributions ? <InlineLoader/> : distributions.length > 0 ? <div>
                                <Heading type="Subsection">Distributions</Heading>
                                <div className="Description">
                                    Distributions represent a specific available form of a dataset. Each dataset might be
                                    available in different forms, these forms might represent different formats of the
                                    dataset
                                    or different endpoints.
                                </div>
                                {distributions.map((distribution) => {
                                        return <ListItem key={distribution.id}
                                                         newWindow={embedded}
                                                         link={{
                                                             pathname: distribution.relativeUrl,
                                                             state: breadcrumbs.current ? breadcrumbs.current.state : null
                                                         }}
                                                         title={localizedText(distribution.metadata.title, 'en')}
                                                         description={localizedText(distribution.metadata.description, 'en')}
                                                         smallIcon={(distribution.accessRights === 2 || distribution.accessRights === 3) && 'lock'}
                                        />
                                    }
                                )}
                            </div> :
                            <div className="NoResults">This dataset does not have any associated distributions.</div>}
                    </div>
                    <div className="SideCol">
                        <MetadataSideBar type="dataset" metadata={dataset.metadata} />
                    </div>
                </>}
            </MainBody>
        </Layout>;
    }
}

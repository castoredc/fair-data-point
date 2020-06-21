import React, {Component} from "react";
import axios from "axios/index";

import {Col, Row} from "react-bootstrap";
import LoadingScreen from "../../../components/LoadingScreen";
import {localizedText} from "../../../util";
import MetadataItem from "../../../components/MetadataItem";
import ListItem from "../../../components/ListItem";
import queryString from "query-string";
import FAIRDataInformation from "../../../components/FAIRDataInformation";
import {MethodType, RecruitmentStatus, StudyType} from "../../../components/MetadataItem/EnumMappings";
import Tags from "../../../components/Tags";
import Contacts from "../../../components/MetadataItem/Contacts";
import Organizations from "../../../components/MetadataItem/Organizations";
import {toast} from "react-toastify";
import ToastContent from "../../../components/ToastContent";
import InlineLoader from "../../../components/LoadingScreen/InlineLoader";

export default class Dataset extends Component {
    constructor(props) {
        super(props);
        this.state = {
            isLoadingDataset:       true,
            hasLoadedDataset:       false,
            isLoadingDistributions: true,
            hasLoadedDistributions: false,
            dataset:                null,
            distributions:          []
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
                toast.error(<ToastContent type="error" message={message} />);
            });
    };

    getDistributions = () => {
        axios.get('/api/dataset/' + this.props.match.params.dataset + '/distribution')
            .then((response) => {
                this.setState({
                    distributions: response.data,
                    isLoadingDistributions: false,
                    hasLoadedDistributions: true
                });
            })
            .catch((error) => {
                this.setState({
                    isLoadingDistributions: false
                });

                const message = (error.response && typeof error.response.data.error !== "undefined") ? error.response.data.error : 'An error occurred while loading the distributions';
                toast.error(<ToastContent type="error" message={message} />);
            });
    };

    render() {
        const { dataset, distributions, isLoadingDataset, isLoadingDistributions } = this.state;
        const { location } = this.props;
        
        const params = queryString.parse(this.props.location.search);
        const embedded = (typeof params.embed !== 'undefined');

        if(isLoadingDataset)
        {
            return <LoadingScreen showLoading={true}/>;
        }

        const fdp = (location.state && 'fdp' in location.state) ? location.state.fdp : null;
        const catalog = (location.state && 'catalog' in location.state) ? location.state.catalog : null;
        const study = (location.state && 'study' in location.state) ? location.state.study : null;
        const breadcrumbs = {fdp: fdp, catalog: catalog, dataset: dataset};

        return <FAIRDataInformation
            embedded={embedded}
            className="Dataset"
            title={localizedText(dataset.metadata.title, 'en')}
            version={dataset.metadata.version.metadata}
            issued={dataset.metadata.issued}
            modified={dataset.metadata.modified}
            license={dataset.metadata.license}
            breadcrumbs={breadcrumbs}
        >
            <Row>
                <Col md={8} className="InformationCol">
                    {dataset.metadata.description && <div className="InformationDescription">{localizedText(dataset.metadata.description, 'en', true)}</div>}

                    {isLoadingDistributions ? <InlineLoader /> : distributions.length > 0 ? <div>
                        <h2>Distributions</h2>
                        <div className="Description">
                            Distributions represent a specific available form of a dataset. Each dataset might be available in different forms, these forms might represent different formats of the dataset or different endpoints.
                        </div>
                        {distributions.map((distribution) => {
                            if(distribution.hasMetadata === false) {
                                return null;
                            }

                            return <ListItem key={distribution.id}
                                             newWindow={embedded}
                                             link={{
                                                 pathname: distribution.relativeUrl,
                                                 state: {...breadcrumbs, study: study}
                                             }}
                                             title={localizedText(distribution.metadata.title, 'en')}
                                             description={localizedText(distribution.metadata.description, 'en')}
                                             smallIcon={(distribution.accessRights === 2 || distribution.accessRights === 3) && 'lock'}
                            />}
                        )}
                    </div> : <div className="NoResults">This dataset does not have any associated distributions.</div>}
                </Col>
                <Col md={4}>
                </Col>
            </Row>
        </FAIRDataInformation>;

        // {this.state.catalog.publishers.length > 0 && <div className="Publishers">
        //     {dataset.metadata.publishers.map((item, index) => {
        //         return <Contact key={index}
        //                         url={item.url}
        //                         type={item.type}
        //                         name={item.name} />}
        //     )}
        // </div>}
    }
}

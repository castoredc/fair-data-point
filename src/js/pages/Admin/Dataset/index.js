import React, {Component} from "react";
import axios from "axios";

import {Col, Row} from "react-bootstrap";
import {localizedText} from "../../../util";
import {LinkContainer} from "react-router-bootstrap";
import InlineLoader from "../../../components/LoadingScreen/InlineLoader";
import Nav from "react-bootstrap/Nav";
import NotFound from "../../NotFound";
import {Route, Switch} from "react-router-dom";
import {toast} from "react-toastify/index";
import ToastContent from "../../../components/ToastContent";
import DatasetDistributions from "../Dataset/DatasetDistributions";
import AddDistribution from "../Dataset/AddDistribution";
import DatasetMetadata from "./DatasetMetadata";
import DatasetDetails from "./DatasetDetails";
import {Button} from "@castoredc/matter";

export default class Dataset extends Component {
    constructor(props) {
        super(props);
        this.state = {
            isLoadingDataset:  true,
            dataset:           null,
            catalog:           props.match.params.catalog
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
                    isLoadingDataset: false
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


    render() {
        const { dataset, isLoadingDataset, catalog } = this.state;

        if(isLoadingDataset) {
            return <InlineLoader />;
        }
        
        const url = '/admin' + (catalog ? '/catalog/' + catalog : '');

        return <div className="PageContainer">
            <Row className="PageHeader">
                <Col sm={2} className="Back">
                    {catalog && <LinkContainer to={url + '/studies'}>
                        <Button buttonType="secondary" icon="arrowLeftChevron">
                            Back to catalog
                        </Button>
                    </LinkContainer>}
                </Col>
                <Col sm={10} className="PageTitle">
                    <div><h3>{dataset.hasMetadata ? localizedText(dataset.metadata.title, 'en') : 'Dataset'}</h3></div>
                </Col>
            </Row>
            <Row className="FillHeight">
                <Col sm={2} className="LeftNav">
                    <Nav className="flex-column">
                        <LinkContainer to={url + '/dataset/' + dataset.slug} exact={true}>
                            <Nav.Link>Dataset</Nav.Link>
                        </LinkContainer>
                        <LinkContainer to={url + '/dataset/' + dataset.slug + '/metadata'} exact={true}>
                            <Nav.Link>Metadata</Nav.Link>
                        </LinkContainer>
                        <hr />
                        <LinkContainer to={url + '/dataset/' + dataset.slug + '/distributions'} exact={true}>
                            <Nav.Link disabled={!dataset.hasMetadata}>Available data</Nav.Link>
                        </LinkContainer>
                    </Nav>
                </Col>
                <Col sm={10} className="Page">
                    <Switch>
                        <Route path="/admin/catalog/:catalog/dataset/:dataset" exact
                               render={(props) => <DatasetDetails {...props} catalog={catalog} dataset={dataset} onSave={this.getDataset} />} />
                        <Route path="/admin/catalog/:catalog/dataset/:dataset/metadata" exact
                               render={(props) => <DatasetMetadata {...props} catalog={catalog} dataset={dataset} onSave={this.getDataset} />} />
                        <Route path="/admin/catalog/:catalog/dataset/:dataset/distributions" exact
                               render={(props) => <DatasetDistributions {...props} catalog={catalog} dataset={dataset} />} />
                        <Route path="/admin/catalog/:catalog/dataset/:dataset/distributions/add" exact
                               render={(props) => <AddDistribution {...props} catalog={catalog} dataset={dataset} />} />

                        <Route path="/admin/dataset/:dataset" exact
                               render={(props) => <DatasetDetails {...props} dataset={dataset} onSave={this.getDataset} />} />
                        <Route path="/admin/dataset/:dataset/metadata" exact
                               render={(props) => <DatasetMetadata {...props} dataset={dataset} onSave={this.getDataset} />} />
                        <Route path="/admin/dataset/:dataset/distributions" exact
                               render={(props) => <DatasetDistributions {...props} dataset={dataset} />} />
                        <Route path="/admin/dataset/:dataset/distributions/add" exact
                               render={(props) => <AddDistribution {...props} dataset={dataset} />} />


                        <Route component={NotFound} />
                    </Switch>
                </Col>
            </Row>
        </div>;
    }
}
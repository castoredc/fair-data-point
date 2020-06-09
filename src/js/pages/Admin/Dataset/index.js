import React, {Component} from "react";
import axios from "axios/index";

import {Col, Row} from "react-bootstrap";
import {localizedText} from "../../../util";
import {LinkContainer} from "react-router-bootstrap";
import Button from "react-bootstrap/Button";
import InlineLoader from "../../../components/LoadingScreen/InlineLoader";
import Icon from "../../../components/Icon";
import Nav from "react-bootstrap/Nav";
import NotFound from "../../NotFound";
import {Route, Switch} from "react-router-dom";
import {toast} from "react-toastify/index";
import ToastContent from "../../../components/ToastContent";
import DatasetDistributions from "../Dataset/DatasetDistributions";
import AddDistribution from "../Dataset/AddDistribution";
import DatasetDetails from "./DatasetDetails";
import DatasetContacts from "./DatasetContacts";
import DatasetOrganizations from "./DatasetOrganizations";
import DatasetConsent from "./DatasetConsent";
import DatasetAnnotations from "./DatasetAnnotations";

export default class Dataset extends Component {
    constructor(props) {
        super(props);
        this.state = {
            isLoadingDataset:  true,
            hasLoadedDataset:  false,
            dataset:           null,
            catalog:           props.match.params.catalog
        };
    }

    componentDidMount() {
        this.getDataset();
    }

    getDataset = () => {
        axios.get('/api/catalog/' + this.props.match.params.catalog + '/dataset/' + this.props.match.params.dataset)
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

                const message = (error.response && typeof error.response.data.message !== "undefined") ? error.response.data.message : 'An error occurred while loading the dataset';
                toast.error(<ToastContent type="error" message={message} />);
            });
    };


    render() {
        const { dataset, isLoadingDataset, catalog } = this.state;

        if(isLoadingDataset) {
            return <InlineLoader />;
        }

        return <div className="PageContainer">
            <Row className="PageHeader">
                <Col sm={2} className="Back">
                    <LinkContainer to={'/admin/catalog/' + catalog + '/studies'}>
                        <Button variant="link" className="BackButton">
                            <Icon type="arrowLeft" /> Back to catalog
                        </Button>
                    </LinkContainer>
                </Col>
                <Col sm={10} className="PageTitle">
                    <div>{dataset.hasMetadata && <h3>{localizedText(dataset.title, 'en')}</h3>}</div>
                </Col>
            </Row>
            <Row>
                <Col sm={2} className="LeftNav">
                    <Nav className="flex-column">
                        <LinkContainer to={'/admin/catalog/' + catalog + '/dataset/' + dataset.slug} exact={true}>
                            <Nav.Link>Study</Nav.Link>
                        </LinkContainer>
                        <LinkContainer to={'/admin/catalog/' + catalog + '/dataset/' + dataset.slug + '/contacts'} exact={true}>
                            <Nav.Link disabled={!dataset.hasMetadata}>Contacts</Nav.Link>
                        </LinkContainer>
                        <LinkContainer to={'/admin/catalog/' + catalog + '/dataset/' + dataset.slug + '/organizations'} exact={true}>
                            <Nav.Link disabled={!dataset.hasMetadata}>Centers</Nav.Link>
                        </LinkContainer>
                        <LinkContainer to={'/admin/catalog/' + catalog + '/dataset/' + dataset.slug + '/consent'} exact={true}>
                            <Nav.Link disabled={!dataset.hasMetadata}>Consent</Nav.Link>
                        </LinkContainer>
                        <hr />
                        <LinkContainer to={'/admin/catalog/' + catalog + '/dataset/' + dataset.slug + '/annotations'} exact={true}>
                            <Nav.Link disabled={!dataset.hasMetadata}>Annotations</Nav.Link>
                        </LinkContainer>
                        <LinkContainer to={'/admin/catalog/' + catalog + '/dataset/' + dataset.slug + '/distributions'} exact={true}>
                            <Nav.Link disabled={!dataset.hasMetadata}>Available data</Nav.Link>
                        </LinkContainer>
                    </Nav>
                </Col>
                <Col sm={10} className="Page">
                    <Switch>
                        <Route path="/admin/catalog/:catalog/dataset/:dataset" exact
                               render={(props) => <DatasetDetails {...props} catalog={catalog} dataset={dataset} onSave={this.getDataset} />} />
                        <Route path="/admin/catalog/:catalog/dataset/:dataset/contacts" exact
                               render={(props) => <DatasetContacts {...props} catalog={catalog} dataset={dataset} />} />
                        <Route path="/admin/catalog/:catalog/dataset/:dataset/organizations" exact
                               render={(props) => <DatasetOrganizations {...props} catalog={catalog} dataset={dataset} />} />
                        <Route path="/admin/catalog/:catalog/dataset/:dataset/consent" exact
                               render={(props) => <DatasetConsent {...props} catalog={catalog} dataset={dataset} />} />
                        <Route path="/admin/catalog/:catalog/dataset/:dataset/distributions" exact
                               render={(props) => <DatasetDistributions {...props} catalog={catalog} dataset={dataset} />} />
                        <Route path="/admin/catalog/:catalog/dataset/:dataset/distributions/add" exact
                               render={(props) => <AddDistribution {...props} catalog={catalog} dataset={dataset} />} />
                        <Route path="/admin/catalog/:catalog/dataset/:dataset/annotations" exact
                               render={(props) => <DatasetAnnotations {...props} studyId={dataset.studyId} catalog={catalog} dataset={dataset} />} />
                        <Route component={NotFound} />
                    </Switch>
                </Col>
            </Row>
        </div>;
    }
}
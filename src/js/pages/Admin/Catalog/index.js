import React, {Component} from "react";
import axios from "axios/index";

import {Col, Row} from "react-bootstrap";
import {localizedText} from "../../../util";
import {LinkContainer} from "react-router-bootstrap";
import InlineLoader from "../../../components/LoadingScreen/InlineLoader";
import Nav from "react-bootstrap/Nav";
import NotFound from "../../NotFound";
import {Route, Switch} from "react-router-dom";
import CatalogStudies from "./CatalogStudies";
import CatalogDetails from "./CatalogDetails";
import {Button} from "@castoredc/matter";
import CatalogDatasets from "./CatalogDatasets";
import CatalogMetadata from "./CatalogMetadata";
import CatalogAddStudy from "./CatalogAddStudy";

export default class Catalog extends Component {
    constructor(props) {
        super(props);
        this.state = {
            isLoadingCatalog:  true,
            catalog:           null,
        };
    }

    componentDidMount() {
        this.getCatalog();
    }

    getCatalog = () => {
        this.setState({
            isLoadingCatalog: true,
        });

        axios.get('/api/catalog/' + this.props.match.params.catalog)
            .then((response) => {
                this.setState({
                    catalog:          response.data,
                    isLoadingCatalog: false,
                });
            })
            .catch((error) => {
                if (error.response && typeof error.response.data.message !== "undefined") {
                    this.setState({
                        isLoadingCatalog: false,
                        hasError:         true,
                        errorMessage:     error.response.data.message,
                    });
                } else {
                    this.setState({
                        isLoadingCatalog: false,
                    });
                }
            });
    };

    render() {
        const { catalog, isLoadingCatalog } = this.state;

        if(isLoadingCatalog) {
            return <InlineLoader />;
        }

        return <div className="PageContainer">
            <Row className="PageHeader">
                <Col sm={2} className="Back">
                    <LinkContainer to={'/admin/catalogs'}>
                        <Button buttonType="secondary" icon="arrowLeftChevron">
                            Back to catalogs
                        </Button>
                    </LinkContainer>
                </Col>
                <Col sm={10} className="PageTitle">
                    <div><h3>{catalog.hasMetadata && localizedText(catalog.metadata.title, 'en')}</h3></div>
                </Col>
            </Row>
            <Row className="FillHeight">
                <Col sm={2} className="LeftNav">
                    <Nav className="flex-column">
                        <LinkContainer to={'/admin/catalog/' + catalog.slug} exact={true}>
                            <Nav.Link>Catalog</Nav.Link>
                        </LinkContainer>
                        <LinkContainer to={'/admin/catalog/' + catalog.slug + '/metadata'} exact={true}>
                            <Nav.Link>Metadata</Nav.Link>
                        </LinkContainer>
                        <hr />
                        <LinkContainer to={'/admin/catalog/' + catalog.slug + '/datasets'} exact={true}>
                            <Nav.Link>Datasets</Nav.Link>
                        </LinkContainer>
                        <LinkContainer to={'/admin/catalog/' + catalog.slug + '/studies'} exact={true}>
                            <Nav.Link>Studies</Nav.Link>
                        </LinkContainer>
                    </Nav>
                </Col>
                <Col sm={10} className="Page">
                    <Switch>
                        <Route path="/admin/catalog/:catalog" exact
                               render={(props) => <CatalogDetails {...props} catalog={catalog} />} />
                        <Route path="/admin/catalog/:catalog/metadata" exact
                               render={(props) => <CatalogMetadata {...props} catalog={catalog} onSave={this.getCatalog} />} />
                        <Route path="/admin/catalog/:catalog/studies/add" exact
                               render={(props) => <CatalogAddStudy {...props} catalog={catalog} />} />
                        <Route path="/admin/catalog/:catalog/studies" exact
                               render={(props) => <CatalogStudies {...props} catalog={catalog} />} />
                        <Route path="/admin/catalog/:catalog/datasets" exact
                               render={(props) => <CatalogDatasets {...props} catalog={catalog} />} />
                        <Route component={NotFound} />
                    </Switch>
                </Col>
            </Row>
        </div>;
    }
}
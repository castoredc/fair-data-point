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
import CatalogStudies from "./CatalogStudies";
import CatalogDetails from "./CatalogDetails";
import AddStudy from "./AddStudy";

export default class Catalog extends Component {
    constructor(props) {
        super(props);
        this.state = {
            isLoadingCatalog:  true,
            hasLoadedCatalog: false,
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
                    hasLoadedCatalog: true,
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
                    <LinkContainer to={'/admin/'}>
                        <Button variant="link" className="BackButton">
                            <Icon type="arrowLeft" /> Back to catalogs
                        </Button>
                    </LinkContainer>
                </Col>
                <Col sm={10} className="PageTitle">
                    <div><h3>{localizedText(catalog.title, 'en')}</h3></div>
                </Col>
            </Row>
            <Row>
                <Col sm={2} className="LeftNav">
                    <Nav className="flex-column">
                        <LinkContainer to={'/admin/catalog/' + catalog.slug} exact={true}>
                            <Nav.Link>Catalog</Nav.Link>
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
                        <Route path="/admin/catalog/:catalog/studies/add" exact
                               render={(props) => <AddStudy {...props} catalog={catalog} />} />
                        <Route path="/admin/catalog/:catalog/studies" exact
                               render={(props) => <CatalogStudies {...props} catalog={catalog} />} />
                        <Route component={NotFound} />
                    </Switch>
                </Col>
            </Row>
        </div>;
    }
}
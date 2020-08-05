import React, {Component} from "react";
import axios from "axios";
import {localizedText} from "../../../util";
import {LinkContainer} from "react-router-bootstrap";
import InlineLoader from "../../../components/LoadingScreen/InlineLoader";
import Nav from "react-bootstrap/Nav";
import NotFound from "../../NotFound";
import {Route, Switch} from "react-router-dom";
import CatalogStudies from "./CatalogStudies";
import CatalogDetails from "./CatalogDetails";
import {ViewHeader} from "@castoredc/matter";
import CatalogDatasets from "./CatalogDatasets";
import CatalogMetadata from "./CatalogMetadata";
import CatalogAddStudy from "./CatalogAddStudy";
import CatalogAddDataset from "./CatalogAddDataset";
import DocumentTitle from "../../../components/DocumentTitle";

export default class Catalog extends Component {
    constructor(props) {
        super(props);
        this.state = {
            isLoadingCatalog: true,
            catalog:          null,
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
                if (error.response && typeof error.response.data.error !== "undefined") {
                    this.setState({
                        isLoadingCatalog: false,
                        hasError:         true,
                        errorMessage:     error.response.data.error,
                    });
                } else {
                    this.setState({
                        isLoadingCatalog: false,
                    });
                }
            });
    };

    render() {
        const {catalog, isLoadingCatalog} = this.state;

        if (isLoadingCatalog) {
            return <InlineLoader/>;
        }

        const title = catalog.hasMetadata ? localizedText(catalog.metadata.title, 'en') : null;

        return <div className="PageContainer">
            <DocumentTitle title={'FDP Admin | Catalog' + (title ? ` | ${title}` : '')}/>

            <div className="LeftNav">
                <Nav className="flex-column">
                    <LinkContainer to={'/admin/catalog/' + catalog.slug} exact={true}>
                        <Nav.Link>Catalog</Nav.Link>
                    </LinkContainer>
                    <LinkContainer to={'/admin/catalog/' + catalog.slug + '/metadata'} exact={true}>
                        <Nav.Link>Metadata</Nav.Link>
                    </LinkContainer>
                    <hr/>
                    <LinkContainer to={'/admin/catalog/' + catalog.slug + '/datasets'} exact={true}>
                        <Nav.Link>Datasets</Nav.Link>
                    </LinkContainer>
                    <LinkContainer to={'/admin/catalog/' + catalog.slug + '/studies'} exact={true}>
                        <Nav.Link>Studies</Nav.Link>
                    </LinkContainer>
                </Nav>
            </div>
            <div className="Page">
                <div className="PageTitle">
                    {title && <ViewHeader>{title}</ViewHeader>}
                </div>

                <Switch>
                    <Route path="/admin/catalog/:catalog" exact
                           render={(props) => <CatalogDetails {...props} catalog={catalog}/>}/>
                    <Route path="/admin/catalog/:catalog/metadata" exact
                           render={(props) => <CatalogMetadata {...props} catalog={catalog}
                                                               onSave={this.getCatalog}/>}/>
                    <Route path="/admin/catalog/:catalog/studies/add" exact
                           render={(props) => <CatalogAddStudy {...props} catalog={catalog}/>}/>
                    <Route path="/admin/catalog/:catalog/studies" exact
                           render={(props) => <CatalogStudies {...props} catalog={catalog}/>}/>
                    <Route path="/admin/catalog/:catalog/datasets" exact
                           render={(props) => <CatalogDatasets {...props} catalog={catalog}/>}/>
                    <Route path="/admin/catalog/:catalog/datasets/add" exact
                           render={(props) => <CatalogAddDataset {...props} catalog={catalog}/>}/>
                    <Route component={NotFound}/>
                </Switch>
            </div>
        </div>;
    }
}
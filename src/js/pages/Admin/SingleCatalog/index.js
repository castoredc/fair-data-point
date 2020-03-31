import React, {Component} from "react";
import axios from "axios/index";

import {Col, Row} from "react-bootstrap";
import LoadingScreen from "../../../components/LoadingScreen";
import {localizedText} from "../../../util";
import MetadataItem from "../../../components/MetadataItem";
import FAIRDataInformation from "../../../components/FAIRDataInformation";
import queryString from "query-string";
import StudyListItem from "../../../components/ListItem/StudyListItem";
import ListItem from "../../../components/ListItem";
import {LinkContainer} from "react-router-bootstrap";
import Button from "react-bootstrap/Button";
import AdminPage from "../../../components/AdminPage";
import AdminStudyListItem from "../../../components/ListItem/AdminStudyListItem";
import Container from "react-bootstrap/Container";

export default class SingleCatalog extends Component {
    constructor(props) {
        super(props);
        this.state = {
            isLoading: true,
            hasLoadedCatalogs:  false,
            hasLoadedDatasets:  false,
            catalog:   null,
            datasets:  [],
        };
    }

    componentDidMount() {
        this.getCatalog();
        this.getDatasets();
    }

    getCatalog = () => {
        this.setState({
            isLoading: true,
        });

        axios.get('/api/catalog/' + this.props.match.params.catalog)
            .then((response) => {
                this.setState({
                    catalog:   response.data,
                    isLoading: false,
                    hasLoadedCatalog:  true,
                });
            })
            .catch((error) => {
                console.log(error);
                if (error.response && typeof error.response.data.message !== "undefined") {
                    this.setState({
                        isLoading:    false,
                        hasError:     true,
                        errorMessage: error.response.data.message,
                    });
                } else {
                    this.setState({
                        isLoading: false,
                    });
                }
            });
    };

    getDatasets = () => {
        this.setState({
            isLoading: true,
        });

        axios.get('/api/catalog/' + this.props.match.params.catalog + '/dataset')
            .then((response) => {
                this.setState({
                    datasets:   response.data,
                    isLoading: false,
                    hasLoadedDatasets:  true,
                });
            })
            .catch((error) => {
                console.log(error);
                if (error.response && typeof error.response.data.message !== "undefined") {
                    this.setState({
                        isLoading:    false,
                        hasError:     true,
                        errorMessage: error.response.data.message,
                    });
                } else {
                    this.setState({
                        isLoading: false,
                    });
                }
            });
    };

    render() {
        if (this.state.isLoading) {
            return <LoadingScreen showLoading={true}/>;
        }

        if(!this.state.hasLoadedCatalog || !this.state.hasLoadedDatasets)
        {
            return <LoadingScreen showLoading={true}/>;
        }

        return <AdminPage
            className="Catalog"
            title={localizedText(this.state.catalog.title, 'en')}
        >
            <Row>
                <Col>
                    <div className="ButtonBar Right">
                        <LinkContainer to={'/admin/' + this.props.match.params.catalog + '/study/add'}>
                            <Button variant="primary">Add study</Button>
                        </LinkContainer>
                    </div>
                    <Container>
                    {this.state.datasets.length > 0 ? this.state.datasets.map((item, index) => {
                            return <AdminStudyListItem    key={index}
                                                          id={item.studyId}
                                                          catalog={this.props.match.params.catalog}
                                                          name={localizedText(item.title, 'en')}
                                                          published={item.published}
                                                          slug={item.slug}
                            />
                        },
                    ) : <div className="NoResults">No studies found.</div>}
                    </Container>
                </Col>
            </Row>
        </AdminPage>;
    }
}
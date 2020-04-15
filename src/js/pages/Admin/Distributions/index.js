import React, {Component} from "react";
import axios from "axios/index";

import {Col, Row} from "react-bootstrap";
import LoadingScreen from "../../../components/LoadingScreen";
import {localizedText} from "../../../util";
import {LinkContainer} from "react-router-bootstrap";
import Button from "react-bootstrap/Button";
import AdminPage from "../../../components/AdminPage";
import Container from "react-bootstrap/Container";
import {toast} from "react-toastify";
import ToastContent from "../../../components/ToastContent";
import AdminDistributionListItem from "../../../components/ListItem/AdminDistributionListItem";
import Icon from "../../../components/Icon";

export default class Distributions extends Component {
    constructor(props) {
        super(props);
        this.state = {
            isLoadingCatalog:       true,
            isLoadingDataset:       true,
            isLoadingDistributions: true,
            hasLoadedCatalog:       false,
            hasLoadedDataset:       false,
            hasLoadedDistributions: false,
            catalog:                null,
            dataset:                null,
            distributions:          [],
        };
    }

    componentDidMount() {
        this.getCatalog();
        this.getDataset();
        this.getDistributions();
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

    getDataset = () => {
        this.setState({
            isLoadingDataset: true,
        });

        axios.get('/api/catalog/' + this.props.match.params.catalog + '/dataset/' + this.props.match.params.dataset)
            .then((response) => {
                this.setState({
                    dataset:          response.data,
                    isLoadingDataset: false,
                    hasLoadedDataset: true,
                });
            })
            .catch((error) => {
                if (error.response && typeof error.response.data.message !== "undefined") {
                    this.setState({
                        isLoadingDataset: false,
                        hasError:         true,
                        errorMessage:     error.response.data.message,
                    });
                } else {
                    this.setState({
                        isLoadingDataset: false,
                    });
                }
            });
    };

    getDistributions = () => {
        this.setState({
            isLoadingDistributions: true,
        });

        axios.get('/api/catalog/' + this.props.match.params.catalog + '/dataset/' + this.props.match.params.dataset + '/distribution')
            .then((response) => {
                this.setState({
                    distributions:          response.data,
                    isLoadingDistributions: false,
                    hasLoadedDistributions: true,
                });
            })
            .catch((error) => {
                this.setState({
                    isLoadingDistributions: false,
                });

                const message = (error.response && typeof error.response.data.message !== "undefined") ? error.response.data.message : 'An error occurred while loading the datasets';
                toast.error(<ToastContent type="error" message={message}/>);
            });
    };

    render() {
        if (!this.state.hasLoadedCatalog || !this.state.hasLoadedDataset || !this.state.hasLoadedDistributions) {
            return <LoadingScreen showLoading={true}/>;
        }

        return <AdminPage
            className="Dataset"
            title={localizedText(this.state.dataset.title, 'en')}
        >
            <Row>
                <Col sm={6}>
                    <div className="ButtonBar">
                        <LinkContainer to={'/admin/' + this.props.match.params.catalog}>
                            <Button variant="link" className="BackButton"><Icon type="arrowLeft" /> {localizedText(this.state.catalog.title, 'en')}</Button>
                        </LinkContainer>
                    </div>
                </Col>
                <Col sm={6}>
                    <div className="ButtonBar Right">
                    </div>
                </Col>
            </Row>
            <Row className="Distributions">
                <Col md={12}>
                    {this.state.distributions.length > 0 ? <Container>
                        <Row className="ListItem AdminListItem AdminListItemHeader">
                            <Col md={8}>Name</Col>
                            <Col md={2}>Type</Col>
                            <Col md={2}/>
                        </Row>

                        {this.state.distributions.map((item, index) => {
                                return <AdminDistributionListItem  key={index}
                                                                   id={item.studyId}
                                                                   catalog={this.props.match.params.catalog}
                                                                   dataset={this.props.match.params.dataset}
                                                                   name={localizedText(item.title, 'en')}
                                                                   slug={item.slug}
                                                                   type={item.type}
                                />
                            },
                        )}
                    </Container> : <div className="NoResults">No distributions found.</div>}
                </Col>
            </Row>
        </AdminPage>;
    }
}
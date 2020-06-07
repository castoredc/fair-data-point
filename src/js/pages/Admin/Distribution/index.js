import React, {Component} from "react";
import axios from "axios/index";

import {Col, Row} from "react-bootstrap";
import {localizedText} from "../../../util";
import {LinkContainer} from "react-router-bootstrap";
import Button from "react-bootstrap/Button";
import {toast} from "react-toastify/index";
import ToastContent from "../../../components/ToastContent";
import Icon from "../../../components/Icon";
import InlineLoader from "../../../components/LoadingScreen/InlineLoader";
import Nav from "react-bootstrap/Nav";
import DatasetDistributions from "../Dataset/DatasetDistributions";
import AddDistribution from "../Dataset/AddDistribution";
import NotFound from "../../NotFound";
import {Route, Switch} from "react-router-dom";
import DistributionDetails from "./DistributionDetails";
import DistributionContents from "./DistributionContents";

export default class Distribution extends Component {
    constructor(props) {
        super(props);
        this.state = {
            isLoadingDistribution: true,
            hasLoadedDistribution: false,
            distribution:          null,
            catalog:               props.match.params.catalog,
            dataset:               props.match.params.dataset
        };
    }

    componentDidMount() {
        this.getDistribution();
    }

    getDistribution = () => {
        this.setState({
            isLoadingDistribution: true,
        });

        axios.get('/api/catalog/' + this.props.match.params.catalog + '/dataset/' + this.props.match.params.dataset + '/distribution/' + this.props.match.params.distribution)
            .then((response) => {
                this.setState({
                    distribution:          response.data,
                    isLoadingDistribution: false,
                    hasLoadedDistribution: true,
                });
            })
            .catch((error) => {
                this.setState({
                    isLoadingDistribution: false,
                });

                const message = (error.response && typeof error.response.data.message !== "undefined") ? error.response.data.message : 'An error occurred while loading the distribution';
                toast.error(<ToastContent type="error" message={message}/>);
            });
    };

    render() {
        const { distribution, isLoadingDistribution, catalog, dataset } = this.state;

        if(isLoadingDistribution) {
            return <InlineLoader />;
        }

        return <div className="PageContainer">
            <Row className="PageHeader">
                <Col sm={2} className="Back">
                    <LinkContainer to={'/admin/catalog/' + catalog + '/dataset/' + dataset}>
                        <Button variant="link" className="BackButton">
                            <Icon type="arrowLeft" /> Back to dataset
                        </Button>
                    </LinkContainer>
                </Col>
                <Col sm={10} className="PageTitle">
                    <div><h3>{localizedText(distribution.title, 'en')}</h3></div>
                </Col>
            </Row>
            <Row>
                <Col sm={2} className="LeftNav">
                    <Nav className="flex-column">
                        <LinkContainer to={'/admin/catalog/' + catalog + '/dataset/' + dataset + '/distribution/' + distribution.slug} exact={true}>
                            <Nav.Link>Distribution</Nav.Link>
                        </LinkContainer>
                        <hr />
                        <LinkContainer to={'/admin/catalog/' + catalog + '/dataset/' + dataset + '/distribution/' + distribution.slug + '/contents'} exact={true}>
                            <Nav.Link>Contents</Nav.Link>
                        </LinkContainer>
                    </Nav>
                </Col>
                <Col sm={10} className="Page">
                    <Switch>
                        <Route path="/admin/catalog/:catalog/dataset/:dataset/distribution/:distribution" exact
                               render={(props) => <DistributionDetails {...props} catalog={catalog} dataset={dataset} distribution={distribution} />} />
                        <Route path="/admin/catalog/:catalog/dataset/:dataset/distribution/:distribution/contents" exact
                               render={(props) => <DistributionContents {...props} catalog={catalog} dataset={dataset} distribution={distribution} />} />
                        <Route component={NotFound} />
                    </Switch>
                </Col>
            </Row>
        </div>;
    }
}
import React, {Component} from "react";
import axios from "axios/index";

import {Col, Row} from "react-bootstrap";
import {localizedText} from "../../../util";
import {LinkContainer} from "react-router-bootstrap";
import Button from "react-bootstrap/Button";
import Container from "react-bootstrap/Container";
import {toast} from "react-toastify/index";
import ToastContent from "../../../components/ToastContent";
import AdminDistributionListItem from "../../../components/ListItem/AdminDistributionListItem";
import Icon from "../../../components/Icon";
import InlineLoader from "../../../components/LoadingScreen/InlineLoader";

export default class DatasetDistributions extends Component {
    constructor(props) {
        super(props);
        this.state = {
            isLoadingDistributions: true,
            hasLoadedDistributions: false,
            distributions:          [],
        };
    }

    componentDidMount() {
        this.getDistributions();
    }

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
        const { isLoadingDistributions } = this.state;
        const { catalog, dataset } = this.props;

        if (isLoadingDistributions) {
            return <InlineLoader />;
        }

        return <div>
            <Row>
                <Col sm={6}>
                </Col>
                <Col sm={6}>
                    <div className="ButtonBar Right">
                        <LinkContainer to={'/admin/catalog/' + catalog + '/dataset/' + dataset.slug + '/distributions/add'}>
                            <Button variant="primary" className="AddButton"><Icon type="add" /> Add distribution</Button>
                        </LinkContainer>
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
                                                                   catalog={catalog}
                                                                   dataset={dataset.slug}
                                                                   name={localizedText(item.title, 'en')}
                                                                   slug={item.slug}
                                                                   type={item.type}
                                />
                            },
                        )}
                    </Container> : <div className="NoResults">No distributions found.</div>}
                </Col>
            </Row>
        </div>;
    }
}
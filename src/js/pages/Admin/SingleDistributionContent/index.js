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
import CSVStudyStructure from "../../../components/StudyStructure/CSVStudyStructure";

export default class SingleDistributionContent extends Component {
    constructor(props) {
        super(props);
        this.state = {
            isLoadingDataset:      true,
            isLoadingDistribution: true,
            isLoadingContents:     true,
            hasLoadedDataset:      false,
            hasLoadedDistribution: false,
            hasLoadedContents:     false,
            dataset:               null,
            distribution:          null,
            includeAll:            null,
            contents:              null,
        };
    }

    componentDidMount() {
        this.getDataset();
        this.getDistribution();
        this.getContents();
    }

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
                if (error.response && typeof error.response.data.error !== "undefined") {
                    toast.error(<ToastContent type="error" message={error.response.data.error}/>);
                } else {
                    toast.error(<ToastContent type="error" message="An error occurred"/>);
                }

                this.setState({
                    isLoadingDataset: false,
                });
            });
    };

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

    getContents = () => {
        this.setState({
            isLoadingContents: true,
        });

        axios.get('/api/catalog/' + this.props.match.params.catalog + '/dataset/' + this.props.match.params.dataset + '/distribution/' + this.props.match.params.distribution + '/contents')
            .then((response) => {
                this.setState({
                    includeAll:        response.data.includeAll,
                    contents:          response.data.elements,
                    isLoadingContents: false,
                    hasLoadedContents: true,
                });
            })
            .catch((error) => {
                this.setState({
                    isLoadingContents: false,
                });

                const message = (error.response && typeof error.response.data.message !== "undefined") ? error.response.data.message : 'An error occurred while loading the distribution';
                toast.error(<ToastContent type="error" message={message}/>);
            });
    };

    render() {
        const {includeAll} = this.state;

        if (!this.state.hasLoadedDataset || !this.state.hasLoadedDistribution || !this.state.hasLoadedContents) {
            return <LoadingScreen showLoading={true}/>;
        }

        return <AdminPage
            className="Distribution"
            title={localizedText(this.state.distribution.title, 'en')}
        >
            <Row>
                <Col sm={6}>
                    <div className="ButtonBar">
                        <LinkContainer
                            to={'/admin/' + this.props.match.params.catalog + '/dataset/' + this.props.match.params.dataset + '/distribution'}>
                            <Button variant="link" className="BackButton"><Icon
                                type="arrowLeft"/> {localizedText(this.state.dataset.title, 'en')}</Button>
                        </LinkContainer>
                    </div>
                </Col>
                <Col sm={6}>
                    <div className="ButtonBar Right">

                    </div>
                </Col>
            </Row>
            <Row className="Test">
                {includeAll ? <Col md={12}>
                    <div className="NoResults">This distribution contains all fields.</div>
                </Col> : <Col md={12}>
                    {this.state.distribution.type === 'csv' && <CSVStudyStructure
                        studyId={this.state.distribution.studyId}
                        distributionContents={this.state.contents}
                        catalog={this.props.match.params.catalog}
                        dataset={this.props.match.params.dataset}
                        distribution={this.props.match.params.distribution}
                    />}
                </Col>
                }
            </Row>
        </AdminPage>;
    }
}
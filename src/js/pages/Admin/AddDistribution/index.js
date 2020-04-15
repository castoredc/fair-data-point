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
import CastorStudyForm from "../../../components/Form/Admin/CastorStudyForm";
import AddDistributionForm from "../../../components/Form/Admin/Distribution/AddDistributionForm";

export default class AddDistribution extends Component {
    constructor(props) {
        super(props);
        this.state = {
            isLoadingDataset:       true,
            hasLoadedDataset:       false,
            dataset:                null,
        };
    }

    componentDidMount() {
        this.getDataset();
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

    render() {
        if (this.state.isLoadingDataset) {
            return <LoadingScreen showLoading={true}/>;
        }

        return <AdminPage
            className="Distribution"
            title={"Add distribution to '" + localizedText(this.state.dataset.title, 'en') + "'"}
        >
            <Row>
                <Col>
                    <AddDistributionForm catalog={this.props.match.params.catalog} dataset={this.props.match.params.dataset} />
                </Col>
            </Row>
        </AdminPage>;
    }
}
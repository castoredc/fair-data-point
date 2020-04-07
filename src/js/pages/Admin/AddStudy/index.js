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

export default class AddStudy extends Component {
    constructor(props) {
        super(props);
        this.state = {
            isLoading: true,
            hasLoadedCatalogs:  false,
            hasLoadedDatasets:  true,
            catalog:   null,
            datasets:  [],
        };
    }

    componentDidMount() {
        this.getCatalog();
        // this.getDatasets();
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

        return <AdminPage
            className="Catalog"
            title={"Add study to " + localizedText(this.state.catalog.title, 'en')}
        >
            <Row>
                <Col>
                    <CastorStudyForm catalog={this.props.match.params.catalog} />
                </Col>
            </Row>
        </AdminPage>;
    }
}
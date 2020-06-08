import React, {Component} from "react";
import axios from "axios/index";

import {Col, Row} from "react-bootstrap";
import {localizedText} from "../../../util";
import ListItem from "../../../components/ListItem";
import InlineLoader from "../../../components/LoadingScreen/InlineLoader";

export default class Catalogs extends Component {
    constructor(props) {
        super(props);
        this.state = {
            isLoading:    true,
            isLoaded:     false,
            hasError:     false,
            catalogs:     {}
        };
    }

    componentDidMount() {
        axios.get('/api/catalog')
            .then((response) => {
                this.setState({
                    catalogs:   response.data,
                    isLoading: false,
                    isLoaded:  true,
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
    }

    render() {
        if (this.state.isLoading) {
            return <InlineLoader />;
        }

        return <div className="PageContainer">
            <Row className="PageHeader">
                <Col sm={12} className="PageTitle">
                    <div><h3>Catalogs</h3></div>
                </Col>
            </Row>
            <Row>
                <Col sm={12} className="Page">
                    {this.state.catalogs.length > 0 ? this.state.catalogs.map((item, index) => {
                            return <ListItem    key={index}
                                                link={'/admin/catalog/' + item.slug}
                                                title={localizedText(item.title, 'en')}
                                                description={localizedText(item.description, 'en')} />
                        },
                    ) : <div className="NoResults">No catalogs found.</div>}
                </Col>
            </Row>
        </div>;
    }
}
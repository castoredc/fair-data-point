import React, {Component} from "react";
import axios from "axios/index";

import {Col, Row} from "react-bootstrap";
import LoadingScreen from "../../../components/LoadingScreen";
import {localizedText} from "../../../util";
import FAIRDataInformation from "../../../components/FAIRDataInformation";
import ListItem from "../../../components/ListItem";
import AdminPage from "../../../components/AdminPage";

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
    }

    render() {
        if (this.state.isLoading) {
            return <LoadingScreen showLoading={true}/>;
        }

        return <AdminPage
            className="Catalog"
            title="Catalogs"
        >
            <Row>
                <Col>
                    {this.state.catalogs.length > 0 ? this.state.catalogs.map((item, index) => {
                            return <ListItem    key={index}
                                                link={'admin/' + item.slug}
                                                title={localizedText(item.title, 'en')}
                                                description={localizedText(item.description, 'en')} />
                        },
                    ) : <div className="NoResults">No catalogs found.</div>}
                </Col>
            </Row>
        </AdminPage>;
    }
}
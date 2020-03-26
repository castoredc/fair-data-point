import React, { Component } from "react";
import axios from "axios/index";

import LoadingScreen from "../../../components/LoadingScreen";
import DocumentTitle from "../../../components/DocumentTitle";
import {localizedText} from "../../../util";
import {Container, Row} from "react-bootstrap";
import Contact from "../../../components/MetadataItem/Contact";
import ListItem from "../../../components/ListItem";
import Button from "react-bootstrap/Button";

export default class MyStudies extends Component {
    constructor(props) {
        super(props);
        this.state = {
            isLoading: true,
            studies: {},
            hasError: false,
            errorMessage: ''
        };
    }

    componentDidMount() {
        axios.get('/api/study')
            .then((response) => {
                this.setState({
                    studies: response.data,
                    isLoading: false
                });
            })
            .catch((error) => {
                if(error.response && typeof error.response.data.message !== "undefined")
                {
                    this.setState({
                        isLoading: false,
                        hasError: true,
                        errorMessage: error.response.data.message
                    });
                } else {
                    this.setState({
                        isLoading: false,
                        hasError: true
                    });
                }
            });
    }

    render() {
        return (
            <div className="MyStudies TopLevelContainer">
                <DocumentTitle title="My studies" />
                {!this.state.isLoading ?
                    <div className="Information">
                        {this.state.hasError ?
                            <div>
                                error
                            </div>
                            : <div>
                                <Row className="InformationHeader">
                                    <Container>
                                        <div className="InformationHeaderTop">
                                            <h1 className="Title">My studies</h1>
                                            <div className="Description">
                                                In this overview [...]
                                            </div>
                                        </div>
                                    </Container>
                                </Row>
                                <Row className="InformationRow">
                                    <Container className="Children Studies">
                                        {this.state.studies.length > 0 ? this.state.studies.map((study) => {
                                                return <ListItem key={study.id}
                                                                 link={'/my-studies/study/' + study.id}
                                                                 title={study.name}
                                                />
                                            }
                                        ) : <div className="NoResults">No studies found.</div>}
                                    </Container>
                                </Row>
                            </div>
                        }
                    </div>
                    : <LoadingScreen showLoading={true}/>
                }
            </div>
        );
    }
}

import React, { Component } from "react";
import axios from "axios/index";

import {Container, Row, Col, Button} from "react-bootstrap";
import LoadingScreen from "../../components/LoadingScreen";
import DocumentTitle from "../../components/DocumentTitle";
import {localizedText} from "../../util";
import MetadataItem from "../../components/MetadataItem";
import ListItem from "../../components/ListItem";
import Icon from "../../components/Icon";
import {Link} from "react-router-dom";
import Contact from "../../components/MetadataItem/Contact";
import Alert from "react-bootstrap/Alert";

export default class Distribution extends Component {
    constructor(props) {
        super(props);
        this.state = {
            isLoading: true,
            isLoaded: false,
            hasError: false,
            errorMessage: '',
            distribution: {
                title: [],
                description: [],
                publishers: [],
                language: '',
                license: '',
                version: '',
                issued: '',
                modified: '',
                homepage: ''
            },
            dataset: {
                title: [],
                description: [],
                publishers: [],
                language: '',
                license: '',
                version: '',
                issued: '',
                modified: '',
                homepage: ''
            },
        };
    }

    componentDidMount() {
        axios.get(window.location.href + '?format=json&ui=true')
            .then((response) => {
                this.setState({
                    distribution: response.data.distribution,
                    dataset: response.data.dataset,
                    isLoading: false,
                    isLoaded: true
                });
            })
            .catch((error) => {
                console.log(error);
                if(error.response && typeof error.response.data.message !== "undefined")
                {
                    this.setState({
                        isLoading: false,
                        hasError: true,
                        errorMessage: error.response.data.message
                    });
                } else {
                    this.setState({
                        isLoading: false
                    });
                }
            });
    }

    render() {
        return (
            <Container className="Catalog">
                {this.state.isLoaded ?
                    <div className="Information">
                        <Alert variant="warning">
                            <strong>Notice</strong> Please be aware that this FAIR Data Point (FDP) is still under development and that the (meta)data in this FDP may be dummy data.
                        </Alert>
                        <div className="InformationHeader">
                            <Link to={this.state.dataset.relative_url} className="LinkTop LinkBack">
                                <Icon type="arrowLeft" />
                                {localizedText(this.state.dataset.title, 'en')}
                            </Link>
                            <a href={window.location.href + '?format=json'} className="LinkTop LinkFileType" target="_blank">
                                JSON
                            </a>
                            <a href={window.location.href + '?format=ttl'} className="LinkTop LinkFileType" target="_blank">
                                Turtle
                            </a>
                        </div>
                        <Row className="InformationRow">
                            <DocumentTitle title={localizedText(this.state.distribution.title, 'en')} />
                            <Col md={4} className="Metadata">
                                <div className="MetadataTop">
                                    <div className="Type">Distribution</div>
                                    <h1 className="Title">{localizedText(this.state.distribution.title, 'en')}</h1>
                                    <div className="Description">
                                        {localizedText(this.state.distribution.description, 'en')}
                                    </div>
                                    {this.state.distribution.publishers.length > 0 && <div className="Publishers">
                                        {this.state.distribution.publishers.map((item, index) => {
                                            return <Contact key={index}
                                                            url={item.url}
                                                            type={item.type}
                                                            name={item.name} />}
                                        )}
                                    </div>}
                                </div>
                                <div className="MetadataBottom">
                                    <MetadataItem label="Version" value={this.state.distribution.version} />
                                    <MetadataItem label="Language" url={this.state.distribution.language.url} value={this.state.distribution.language.name} />
                                    <MetadataItem label="License" url={this.state.distribution.license.url} value={this.state.distribution.license.name} />
                                    <MetadataItem label="Issued" value={this.state.distribution.issued.date} />
                                    <MetadataItem label="Modified" value={this.state.distribution.modified.date} />
                                </div>

                            </Col>
                            <Col md={8} className="Children Access">
                                <ListItem link={this.state.distribution.access_url}
                                          title="Access the data"
                                          description="Get access to the distribution." />

                                <ListItem link={this.state.distribution.download_url}
                                          title="Download the data"
                                          description="Get a downloadable file for this distribution." />


                            </Col>

                        </Row>
                    </div>
                    : <LoadingScreen showLoading={true}/>
                }
            </Container>
        );
    }
}

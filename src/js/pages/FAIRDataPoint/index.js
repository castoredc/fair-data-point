import React, { Component } from "react";
import axios from "axios/index";

import {Container, Row, Col, Button} from "react-bootstrap";
import LoadingScreen from "../../components/LoadingScreen";
import DocumentTitle from "../../components/DocumentTitle";
import {localizedText} from "../../util";
import MetadataItem from "../../components/MetadataItem";
import ListItem from "../../components/ListItem";

export default class FAIRDataPoint extends Component {
    constructor(props) {
        super(props);
        this.state = {
            isLoading: true,
            isLoaded: false,
            hasError: false,
            errorMessage: '',
            fdp: {
                title: [],
                description: [],
                publishers: [],
                language: '',
                license: '',
                version: '',
                catalogs: []
            }
        };
    }

    componentDidMount() {
        axios.get(window.location.href + '?format=json')
            .then((response) => {
                this.setState({
                    fdp: response.data.fdp,
                    isLoading: false,
                    isLoaded: true
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
                        isLoading: false
                    });
                }
            });
    }

    render() {
        return (
            <Container className="FAIRDataPoint">
                {this.state.isLoaded ?
                    <div className="Information">
                        <div className="InformationHeader">
                            <a href={window.location.href + '?format=json'} className="LinkTop LinkFileType" target="_blank">
                                JSON
                            </a>
                            <a href={window.location.href + '?format=ttl'} className="LinkTop LinkFileType" target="_blank">
                                RDF
                            </a>
                        </div>
                        <Row className="InformationRow">
                            <DocumentTitle title={localizedText(this.state.fdp.title, 'en')} />
                            <Col md={4} className="Metadata">
                                <div className="MetadataTop">
                                    <h1 className="Title">{localizedText(this.state.fdp.title, 'en')}</h1>
                                    <div className="Description">
                                        {localizedText(this.state.fdp.description, 'en')}
                                    </div>
                                </div>
                                <div className="MetadataBottom">
                                    <MetadataItem label="Version" value={this.state.fdp.version} />
                                    <MetadataItem label="Language" value={this.state.fdp.language.name} />
                                    <MetadataItem label="License" value={this.state.fdp.license} />
                                </div>

                            </Col>
                            <Col md={8} className="Children Catalogs">
                                <h2>Catalogs</h2>
                                {this.state.fdp.catalogs.length > 0 ? this.state.fdp.catalogs.map((item, index) => {
                                    return <ListItem key={index}
                                                     link={item.relative_url}
                                                     title={localizedText(item.title, 'en')}
                                                     description={localizedText(item.description, 'en')} />}
                                ) : <div className="NoResults">No catalogs found.</div>}
                            </Col>

                        </Row>
                    </div>
                    : <LoadingScreen showLoading={true}/>
                }
            </Container>
        );
    }
}

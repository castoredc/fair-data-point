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

export default class Dataset extends Component {
    constructor(props) {
        super(props);
        this.state = {
            isLoading: true,
            isLoaded: false,
            hasError: false,
            errorMessage: '',
            dataset: {
                title: [],
                description: [],
                publishers: [],
                language: '',
                license: '',
                version: '',
                issued: '',
                modified: '',
                homepage: '',
                distributions: []
            },
            catalog: {
                title: [],
                description: [],
                publishers: [],
                language: '',
                license: '',
                version: '',
                issued: '',
                modified: '',
                homepage: ''
            }
        };
    }

    componentDidMount() {
        axios.get(window.location.href + '?format=json')
            .then((response) => {
                this.setState({
                    dataset: response.data.dataset,
                    catalog: response.data.catalog,
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
                        <div className="InformationHeader">
                            <Link to={this.state.catalog.relative_url} className="LinkTop LinkBack">
                                <Icon type="arrowLeft" />
                                {localizedText(this.state.catalog.title, 'en')}
                            </Link>
                            <a href={window.location.href + '?format=json'} className="LinkTop LinkFileType" target="_blank">
                                JSON
                            </a>
                            <a href={window.location.href + '?format=ttl'} className="LinkTop LinkFileType" target="_blank">
                                RDF
                            </a>
                        </div>
                        <Row className="InformationRow">
                            <DocumentTitle title={localizedText(this.state.dataset.title, 'en')} />
                            <Col md={4} className="Metadata">
                                <div className="MetadataTop">
                                    <h1 className="Title">{localizedText(this.state.dataset.title, 'en')}</h1>
                                    <div className="Description">
                                        {localizedText(this.state.dataset.description, 'en')}
                                    </div>
                                </div>
                                <div className="MetadataBottom">
                                    <MetadataItem label="Version" value={this.state.dataset.version} />
                                    <MetadataItem label="Language" value={this.state.dataset.language.name} />
                                    <MetadataItem label="License" value={this.state.dataset.license} />
                                    <MetadataItem label="Issued" value={this.state.dataset.issued.date} />
                                    <MetadataItem label="Modified" value={this.state.dataset.modified.date} />
                                    <MetadataItem label="Landing page" value={this.state.dataset.landingpage} />
                                </div>

                            </Col>
                            <Col md={8} className="Children Catalogs">
                                <h2>Distributions</h2>
                                {this.state.dataset.distributions.length > 0 ? this.state.dataset.distributions.map((item, index) => {
                                    return <ListItem key={index}
                                                     link={item.relative_url}
                                                     title={localizedText(item.title, 'en')}
                                                     description={localizedText(item.description, 'en')} />}
                                ) : <div className="NoResults">No distributions found.</div>}
                            </Col>

                        </Row>
                    </div>
                    : <LoadingScreen showLoading={true}/>
                }
            </Container>
        );
    }
}

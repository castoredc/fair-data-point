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

export default class Catalog extends Component {
    constructor(props) {
        super(props);
        this.state = {
            isLoading: true,
            isLoaded: false,
            hasError: false,
            errorMessage: '',
            catalog: {
                title: [],
                description: [],
                publishers: [],
                language: '',
                license: '',
                version: '',
                issued: '',
                modified: '',
                homepage: '',
                datasets: []
            },
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
        axios.get(window.location.href + '?format=json&ui=true')
            .then((response) => {
                this.setState({
                    catalog: response.data.catalog,
                    fdp: response.data.fdp,
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
                            <Link to={this.state.fdp.relative_url} className="LinkTop LinkBack">
                                <Icon type="arrowLeft" />
                                {localizedText(this.state.fdp.title, 'en')}
                            </Link>
                            <a href={window.location.href + '?format=json'} className="LinkTop LinkFileType" target="_blank">
                                JSON
                            </a>
                            <a href={window.location.href + '?format=ttl'} className="LinkTop LinkFileType" target="_blank">
                                RDF
                            </a>
                        </div>
                        <Row className="InformationRow">
                            <DocumentTitle title={localizedText(this.state.catalog.title, 'en')} />
                            <Col md={4} className="Metadata">
                                <div className="MetadataTop">
                                    <div className="Type">Catalog</div>
                                    <h1 className="Title">{localizedText(this.state.catalog.title, 'en')}</h1>
                                    <div className="Description">
                                        {localizedText(this.state.catalog.description, 'en')}
                                    </div>
                                    {this.state.catalog.publishers.length > 0 && <div className="Publishers">
                                        {this.state.catalog.publishers.map((item, index) => {
                                            return <Contact key={index}
                                                            url={item.url}
                                                            type={item.type}
                                                            name={item.name} />}
                                        )}
                                    </div>}
                                </div>
                                <div className="MetadataBottom">
                                    <MetadataItem label="Version" value={this.state.catalog.version} />
                                    <MetadataItem label="Language" url={this.state.catalog.language.url} value={this.state.catalog.language.name} />
                                    <MetadataItem label="License" url={this.state.catalog.license.url} value={this.state.catalog.license.name} />
                                    <MetadataItem label="Issued" value={this.state.catalog.issued.date} />
                                    <MetadataItem label="Modified" value={this.state.catalog.modified.date} />
                                    <MetadataItem label="Homepage" value={this.state.catalog.homepage} />
                                </div>

                            </Col>
                            <Col md={8} className="Children Datasets">
                                <h2>Datasets</h2>
                                <div className="Description">
                                    Datasets are published collections of data.
                                </div>
                                {this.state.catalog.datasets.length > 0 ? this.state.catalog.datasets.map((item, index) => {
                                    return <ListItem key={index}
                                                     link={item.relative_url}
                                                     title={localizedText(item.title, 'en')}
                                                     description={localizedText(item.description, 'en')} />}
                                ) : <div className="NoResults">No datasets found.</div>}
                            </Col>

                        </Row>
                    </div>
                    : <LoadingScreen showLoading={true}/>
                }
            </Container>
        );
    }
}

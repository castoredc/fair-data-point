import React, { Component } from "react";
import axios from "axios/index";

import {Container, Row, Col, Button} from "react-bootstrap";
import LoadingScreen from "../../components/LoadingScreen";
import DocumentTitle from "../../components/DocumentTitle";
import {localizedText} from "../../util";
import MetadataItem from "../../components/MetadataItem";
import ListItem from "../../components/ListItem";

export default class Catalog extends Component {
    constructor(props) {
        super(props);
        this.state = {
            isLoading: true,
            isLoaded: false,
            hasError: false,
            errorMessage: '',
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
        };
    }

    componentDidMount() {
        axios.get(window.location.href + '?format=json')
            .then((response) => {
                this.setState({
                    title: response.data.catalog.title,
                    description: response.data.catalog.description,
                    publishers: response.data.catalog.publishers,
                    language: response.data.catalog.language,
                    license: response.data.catalog.license,
                    version: response.data.catalog.version,
                    datasets: response.data.catalog.datasets,
                    issued: response.data.catalog.issued,
                    modified: response.data.catalog.modified,
                    homepage: response.data.catalog.homepage,
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
                        <Row className="InformationRow">
                            <DocumentTitle title={localizedText(this.state.title, 'en')} />
                            {/*<div className="RDFRenderHeader">*/}
                            {/*    <Logo />*/}
                            {/*</div>*/}
                            <Col md={4} className="Metadata">
                                <div className="MetadataTop">
                                    <h1 className="Title">{localizedText(this.state.title, 'en')}</h1>
                                    <div className="Description">
                                        {localizedText(this.state.description, 'en')}
                                    </div>
                                </div>
                                <div className="MetadataBottom">
                                    <MetadataItem label="Version" value={this.state.version} />
                                    <MetadataItem label="Language" value={this.state.language.name} />
                                    <MetadataItem label="License" value={this.state.license} />
                                    <MetadataItem label="Issued" value={this.state.issued.date} />
                                    <MetadataItem label="Modified" value={this.state.modified.date} />
                                    <MetadataItem label="Homepage" value={this.state.homepage} />
                                </div>

                            </Col>
                            <Col md={8} className="Children Catalogs">
                                <h2>Datasets</h2>
                                {this.state.datasets.length > 0 ? this.state.datasets.map((item, index) => {
                                    return <ListItem key={index}
                                                     link={item.access_url}
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

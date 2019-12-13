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
            showMetadata: false,
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

    toggleMetadata = (e) => {
        var showMetadata = !this.state.showMetadata;
        this.setState({
            showMetadata: showMetadata
        });

        e.preventDefault();
        return false;
    };

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
            <div className="Catalog TopLevelContainer">
                {this.state.isLoaded ?
                    <div className="Information">
                        <DocumentTitle title={localizedText(this.state.distribution.title, 'en')} />
                        <div className="InformationHeader">
                            {/*<Link to={this.state.dataset.relative_url} className="LinkTop LinkBack">*/}
                            {/*    <Icon type="arrowLeft" />*/}
                            {/*    {localizedText(this.state.dataset.title, 'en')}*/}
                            {/*</Link>*/}
                            <Container>
                                <div className="InformationHeaderTop">
                                    {this.state.distribution.publishers.length > 0 && <div className="Publishers">
                                        {this.state.distribution.publishers.map((item, index) => {
                                            return <Contact key={index}
                                                            url={item.url}
                                                            type={item.type}
                                                            name={item.name} />}
                                        )}
                                    </div>}
                                    <h1 className="Title">{localizedText(this.state.distribution.title, 'en')}</h1>
                                    <div className="Description">
                                        {localizedText(this.state.distribution.description, 'en')}
                                    </div>
                                </div>
                            </Container>
                        </div>
                        <div className="MetadataRow">
                            <Container className="MetadataContainer">
                                {this.state.showMetadata ? <div className="Metadata Shown">

                                    <a href="#" className="MetadataButton" onClick={this.toggleMetadata}>
                                        Hide metadata <Icon type="arrowDown" className="Rotated" />
                                    </a>

                                    <Row>
                                        <MetadataItem className="col-md-6" label="Version" value={this.state.distribution.version} />
                                        <MetadataItem className="col-md-6" label="Language" url={this.state.distribution.language.url} value={this.state.distribution.language.name} />
                                        <MetadataItem className="col-md-6" label="License" url={this.state.distribution.license.url} value={this.state.distribution.license.name} />
                                        <MetadataItem className="col-md-6" label="Issued" value={this.state.distribution.issued.date} />
                                        <MetadataItem className="col-md-6" label="Modified" value={this.state.distribution.modified.date} />
                                    </Row>

                                </div> : <div className="Metadata Hidden">
                                    <a href="#" className="MetadataButton" onClick={this.toggleMetadata}>
                                        Show metadata <Icon type="arrowDown" />
                                    </a>
                                </div>}
                            </Container>
                        </div>
                        <Row className="InformationRow">
                            <Container className="Children Access">
                                <ListItem link={this.state.distribution.access_url}
                                          title="Access the data"
                                          description="Get access to the distribution." />

                                <ListItem link={this.state.distribution.download_url}
                                          title="Download the data"
                                          description="Get a downloadable file for this distribution." />
                            </Container>
                        </Row>
                    </div>
                    : <LoadingScreen showLoading={true}/>
                }
            </div>
        );
    }
}

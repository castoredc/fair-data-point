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

export default class Dataset extends Component {
    constructor(props) {
        super(props);
        this.state = {
            isLoading: true,
            isLoaded: false,
            hasError: false,
            showMetadata: false,
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
        axios.get(window.location.href + '?format=json&ui=true')
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

    toggleMetadata = (e) => {
        var showMetadata = !this.state.showMetadata;
        this.setState({
            showMetadata: showMetadata
        });

        e.preventDefault();
        return false;
    };

    render() {
        return (
            <div className="Catalog TopLevelContainer">
                {this.state.isLoaded ?
                    <div className="Information">
                        <DocumentTitle title={localizedText(this.state.dataset.title, 'en')} />
                        <div className="InformationHeader">
                            {/*<Link to={this.state.catalog.relative_url} className="LinkTop LinkBack">*/}
                            {/*    <Icon type="arrowLeft" />*/}
                            {/*    {localizedText(this.state.catalog.title, 'en')}*/}
                            {/*</Link>*/}
                            <Container>
                                <div className="InformationHeaderTop">
                                    {this.state.catalog.publishers.length > 0 && <div className="Publishers">
                                        {this.state.dataset.publishers.map((item, index) => {
                                            return <Contact key={index}
                                                            url={item.url}
                                                            type={item.type}
                                                            name={item.name} />}
                                        )}
                                    </div>}
                                    <h1 className="Title">{localizedText(this.state.dataset.title, 'en')}</h1>
                                </div>
                                <div className="Description">
                                    {localizedText(this.state.dataset.description, 'en')}
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
                                        <MetadataItem className="col-md-6" label="Version" value={this.state.dataset.version} />
                                        <MetadataItem className="col-md-6" label="Language" url={this.state.dataset.language.url} value={this.state.dataset.language.name} />
                                        <MetadataItem className="col-md-6" label="License" url={this.state.dataset.license.url} value={this.state.dataset.license.name} />
                                        <MetadataItem className="col-md-6" label="Issued" value={this.state.dataset.issued.date} />
                                        <MetadataItem className="col-md-6" label="Modified" value={this.state.dataset.modified.date} />
                                        <MetadataItem className="col-md-6" label="Landing page" value={this.state.dataset.landingpage} />
                                        <MetadataItem className="col-md-6" label="Contact point(s)">
                                            {this.state.dataset.contactPoints.map((item, index) => {
                                                return <Contact key={index}
                                                                url={item.url}
                                                                type={item.type}
                                                                name={item.name} />}
                                            )}
                                        </MetadataItem>
                                    </Row>

                                </div> : <div className="Metadata Hidden">
                                    <a href="#" className="MetadataButton" onClick={this.toggleMetadata}>
                                        Show metadata <Icon type="arrowDown" />
                                    </a>
                                </div>}
                            </Container>
                        </div>
                        <Row className="InformationRow">
                            <Container className="Children Distributions">
                                <h2>Distributions</h2>
                                <div className="Description">
                                    Distributions represent a specific available form of a dataset. Each dataset might be available in different forms, these forms might represent different formats of the dataset or different endpoints.
                                </div>
                                {this.state.dataset.distributions.length > 0 ? this.state.dataset.distributions.map((item, index) => {
                                    return <ListItem key={index}
                                                     link={item.relative_url}
                                                     title={localizedText(item.title, 'en')}
                                                     description={localizedText(item.description, 'en')} />}
                                ) : <div className="NoResults">No distributions found.</div>}
                            </Container>

                        </Row>
                    </div>
                    : <LoadingScreen showLoading={true}/>
                }
            </div>
        );
    }
}

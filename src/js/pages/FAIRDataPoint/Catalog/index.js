import React, { Component } from "react";
import axios from "axios/index";

import {Container, Row, Col, Button} from "react-bootstrap";
import LoadingScreen from "../../../components/LoadingScreen";
import DocumentTitle from "../../../components/DocumentTitle";
import {localizedText} from "../../../util";
import MetadataItem from "../../../components/MetadataItem";
import ListItem from "../../../components/ListItem";
import Icon from "../../../components/Icon";
import {Link} from "react-router-dom";
import Contact from "../../../components/MetadataItem/Contact";
import Alert from "react-bootstrap/Alert";
import FAIRDataInformation from "../../../components/FAIRDataInformation";
import queryString from "query-string";

export default class Catalog extends Component {
    constructor(props) {
        super(props);
        this.state = {
            isLoading: true,
            isLoaded: false,
            hasError: false,
            showMetadata: false,
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
                datasets: [],
                logo: ''
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

    toggleMetadata = (e) => {
        var showMetadata = !this.state.showMetadata;
        this.setState({
            showMetadata: showMetadata
        });

        e.preventDefault();
        return false;
    };

    render() {
        const params = queryString.parse(this.props.location.search);
        const embedded = (typeof params.embed !== 'undefined');

        if(this.state.isLoading)
        {
            return <LoadingScreen showLoading={true}/>;
        }

        return <FAIRDataInformation
            embedded={embedded}
            className="Catalog"
            title={localizedText(this.state.catalog.title, 'en')}
            description={localizedText(this.state.catalog.description, 'en')}
            logo={this.state.catalog.logo}
            >
            <h2>Datasets</h2>
            <div className="Description">
                Datasets are published collections of data.
            </div>
            {this.state.catalog.datasets.length > 0 ? this.state.catalog.datasets.map((item, index) => {
                    return <ListItem key={index}
                                     newWindow={embedded}
                                     link={item.relative_url}
                                     title={localizedText(item.title, 'en')}
                                     description={localizedText(item.description, 'en')}/>
                }
            ) : <div className="NoResults">No datasets found.</div>}
        </FAIRDataInformation>;



        {/*<div className="MetadataRow">*/}
        {/*    <Container className="MetadataContainer">*/}
        {/*        {this.state.showMetadata ? <div className="Metadata Shown">*/}
        {/*            <a href="#" className="MetadataButton" onClick={this.toggleMetadata}>*/}
        {/*                Hide metadata <Icon type="arrowDown" className="Rotated"/>*/}
        {/*            </a>*/}

        {/*            <Row>*/}
        {/*                <MetadataItem className="col-md-6" label="Version"*/}
        {/*                              value={this.state.catalog.version}/>*/}
        {/*                <MetadataItem className="col-md-6" label="Language"*/}
        {/*                              url={this.state.catalog.language.url}*/}
        {/*                              value={this.state.catalog.language.name}/>*/}
        {/*                <MetadataItem className="col-md-6" label="License"*/}
        {/*                              url={this.state.catalog.license.url}*/}
        {/*                              value={this.state.catalog.license.name}/>*/}
        {/*                <MetadataItem className="col-md-6" label="Issued"*/}
        {/*                              value={this.state.catalog.issued.date}/>*/}
        {/*                <MetadataItem className="col-md-6" label="Modified"*/}
        {/*                              value={this.state.catalog.modified.date}/>*/}
        {/*                <MetadataItem className="col-md-6" label="Homepage"*/}
        {/*                              value={this.state.catalog.homepage}/>*/}
        {/*            </Row>*/}
        {/*        </div> : <div className="Metadata Hidden">*/}
        {/*            <a href="#" className="MetadataButton" onClick={this.toggleMetadata}>*/}
        {/*                Show metadata <Icon type="arrowDown"/>*/}
        {/*            </a>*/}
        {/*        </div>}*/}
        {/*    </Container>*/}
        {/*</div>*/}


        {/*{this.state.catalog.publishers.length > 0 && <div className="Publishers">*/}
        {/*    {this.state.catalog.publishers.map((item, index) => {*/}
        {/*            return <Contact key={index}*/}
        {/*                            url={item.url}*/}
        {/*                            type={item.type}*/}
        {/*                            name={item.name}/>*/}
        {/*        }*/}
        {/*    )}*/}
        {/*</div>}*/}
    }
}

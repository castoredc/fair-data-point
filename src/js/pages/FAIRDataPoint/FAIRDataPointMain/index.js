import React, { Component } from "react";
import axios from "axios/index";

import {Container, Row, Col, Button} from "react-bootstrap";
import LoadingScreen from "../../../components/LoadingScreen";
import DocumentTitle from "../../../components/DocumentTitle";
import {localizedText} from "../../../util";
import MetadataItem from "../../../components/MetadataItem";
import ListItem from "../../../components/ListItem";
import Contact from "../../../components/MetadataItem/Contact";
import Alert from "react-bootstrap/Alert";
import Icon from "../../../components/Icon";
import FAIRDataInformation from "../../../components/FAIRDataInformation";
import queryString from "query-string";

export default class FAIRDataPointMain extends Component {
    constructor(props) {
        super(props);
        this.state = {
            isLoading: true,
            isLoaded: false,
            hasError: false,
            showMetadata: false,
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
        axios.get(window.location.href + '?format=json&ui=true')
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
            className="FAIRDataPoint"
            title={localizedText(this.state.fdp.title, 'en')}
            description={localizedText(this.state.fdp.description, 'en')}
        >
            <h2>Catalogs</h2>
            <div className="Description">
                Catalogs are collections of datasets.
            </div>
            {this.state.fdp.catalogs.length > 0 ? this.state.fdp.catalogs.map((item, index) => {
                return <ListItem key={index}
                                 newWindow={embedded}
                                 link={item.relative_url}
                                 title={localizedText(item.title, 'en')}
                                 description={localizedText(item.description, 'en')} />}
            ) : <div className="NoResults">No catalogs found.</div>}
        </FAIRDataInformation>;

        // {this.state.fdp.publishers.length > 0 && <div className="Publishers">
        //     {this.state.fdp.publishers.map((item, index) => {
        //         return <Contact key={index}
        //                         url={item.url}
        //                         type={item.type}
        //                         name={item.name} />}
        //     )}
        // </div>}

        {/*<div className="MetadataRow">*/}
        {/*    <Container className="MetadataContainer">*/}
        {/*        {this.state.showMetadata ? <div className="Metadata Shown">*/}

        {/*            <a href="#" className="MetadataButton" onClick={this.toggleMetadata}>*/}
        {/*                Hide metadata <Icon type="arrowDown" className="Rotated" />*/}
        {/*            </a>*/}

        {/*            <Row>*/}
        {/*                <MetadataItem className="col-md-6" label="Version" value={this.state.fdp.version} />*/}
        {/*                <MetadataItem className="col-md-6" label="Language" url={this.state.fdp.language.url} value={this.state.fdp.language.name} />*/}
        {/*                <MetadataItem className="col-md-6" label="License" url={this.state.fdp.license.url} value={this.state.fdp.license.name} />*/}
        {/*            </Row>*/}

        {/*        </div> : <div className="Metadata Hidden">*/}
        {/*            <a href="#" className="MetadataButton" onClick={this.toggleMetadata}>*/}
        {/*                Show metadata <Icon type="arrowDown" />*/}
        {/*            </a>*/}
        {/*        </div>}*/}
        {/*    </Container>*/}
        {/*</div>*/}
    }
}

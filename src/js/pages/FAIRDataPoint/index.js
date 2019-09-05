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
            title: [],
            description: [],
            publishers: [],
            language: '',
            license: '',
            version: '',
            catalogs: []
        };
    }

    componentDidMount() {
        axios.get(window.location.href + '?format=json')
            .then((response) => {
                this.setState({
                    title: response.data.fdp.title,
                    description: response.data.fdp.description,
                    publishers: response.data.fdp.publishers,
                    language: response.data.fdp.language,
                    license: response.data.fdp.license,
                    version: response.data.fdp.version,
                    catalogs: response.data.fdp.catalogs,
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
                                </div>

                            </Col>
                            <Col md={8} className="Children Catalogs">
                                <h2>Catalogs</h2>
                                {this.state.catalogs.length > 0 ? this.state.catalogs.map((item, index) => {
                                    return <ListItem key={index}
                                                     link={item.access_url}
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

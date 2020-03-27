import React, {Component} from "react";
import axios from "axios/index";

import {Col, Row} from "react-bootstrap";
import LoadingScreen from "../../../components/LoadingScreen";
import {localizedText} from "../../../util";
import ListItem from "../../../components/ListItem";
import Alert from "../../../components/Alert";
import queryString from "query-string";
import FAIRDataInformation from "../../../components/FAIRDataInformation";

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
                homepage: '',
                accessRights: 0
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
                homepage: '',
                logo: ''
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
        let restricted = false;

        if(this.state.distribution.accessRights === 2 || this.state.distribution.accessRights === 3)
        {
            restricted = true;
        }

        const params = queryString.parse(this.props.location.search);
        const embedded = (typeof params.embed !== 'undefined');

        if(this.state.isLoading)
        {
            return <LoadingScreen showLoading={true}/>;
        }

        return <FAIRDataInformation
            embedded={embedded}
            className="Dataset"
            title={localizedText(this.state.distribution.title, 'en')}
            version={this.state.distribution.version}
            issued={this.state.distribution.issued}
            modified={this.state.distribution.modified}
            license={this.state.distribution.license}
        >
            <Row>
                <Col md={8}>
                    {this.state.distribution.description && <div className="InformationDescription">{localizedText(this.state.distribution.description, 'en', true)}</div>}

                    {restricted && <Alert
                        variant="info"
                        icon="lock"
                        message="The access to this distribution is restricted. When you try to access the data, you will be redirected to Castor EDC to authenticate yourself."/>
                    }
                    <ListItem link={this.state.distribution.access_url}
                              title="Access the data"
                              description="Get access to the distribution."
                              smallIcon={restricted && 'lock'} />

                    <ListItem link={this.state.distribution.download_url}
                              title="Download the data"
                              description="Get a downloadable file for this distribution."
                              smallIcon={restricted && 'lock'} />
                </Col>
                <Col md={4}>
                    {this.state.dataset.logo !== '' && <div className="InformationLogo">
                        <img src={this.state.dataset.logo} alt={'Logo'}/>
                    </div>}
                    {/*{this.state.distribution.language && <MetadataItem label="Language" url={this.state.distribution.language.url} value={this.state.distribution.language.name} />}*/}
                </Col>
            </Row>
        </FAIRDataInformation>;

        {/*{this.state.distribution.publishers.length > 0 && <div className="Publishers">*/}
        {/*    {this.state.distribution.publishers.map((item, index) => {*/}
        {/*        return <Contact key={index}*/}
        {/*                        url={item.url}*/}
        {/*                        type={item.type}*/}
        {/*                        name={item.name} />}*/}
        {/*    )}*/}
        {/*</div>}*/}
    }
}

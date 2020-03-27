import React, {Component} from "react";
import axios from "axios/index";

import {Col, Row} from "react-bootstrap";
import LoadingScreen from "../../../components/LoadingScreen";
import {localizedText} from "../../../util";
import MetadataItem from "../../../components/MetadataItem";
import FAIRDataInformation from "../../../components/FAIRDataInformation";
import queryString from "query-string";
import StudyListItem from "../../../components/ListItem/StudyListItem";

export default class Catalog extends Component {
    constructor(props) {
        super(props);
        this.state = {
            isLoading:    true,
            isLoaded:     false,
            hasError:     false,
            showMetadata: false,
            errorMessage: '',
            catalog:      {
                title:       [],
                description: [],
                publishers:  [],
                language:    '',
                license:     '',
                version:     '',
                issued:      '',
                modified:    '',
                homepage:    '',
                datasets:    [],
                logo:        '',
            },
            fdp:          {
                title:       [],
                description: [],
                publishers:  [],
                language:    '',
                license:     '',
                version:     '',
                catalogs:    [],
            },
        };
    }

    componentDidMount() {
        axios.get(window.location.href + '?format=json&ui=true')
            .then((response) => {
                this.setState({
                    catalog:   response.data.catalog,
                    fdp:       response.data.fdp,
                    isLoading: false,
                    isLoaded:  true,
                });
            })
            .catch((error) => {
                console.log(error);
                if (error.response && typeof error.response.data.message !== "undefined") {
                    this.setState({
                        isLoading:    false,
                        hasError:     true,
                        errorMessage: error.response.data.message,
                    });
                } else {
                    this.setState({
                        isLoading: false,
                    });
                }
            });
    }

    toggleMetadata = (e) => {
        var showMetadata = !this.state.showMetadata;
        this.setState({
            showMetadata: showMetadata,
        });

        e.preventDefault();
        return false;
    };

    render() {
        const params = queryString.parse(this.props.location.search);
        const embedded = (typeof params.embed !== 'undefined');

        if (this.state.isLoading) {
            return <LoadingScreen showLoading={true}/>;
        }

        return <FAIRDataInformation
            embedded={embedded}
            className="Catalog"
            title={localizedText(this.state.catalog.title, 'en')}
            version={this.state.catalog.version}
            issued={this.state.catalog.issued}
            modified={this.state.catalog.modified}
            license={this.state.catalog.license}
        >
            <Row>
                <Col>
                    {(this.state.catalog.description && !embedded) && <div
                        className="InformationDescription">{localizedText(this.state.catalog.description, 'en', true)}</div>}

                    {/*<h2>Datasets</h2>*/}
                    {/*<div className="Description">*/}
                    {/*    Datasets are published collections of data.*/}
                    {/*</div>*/}
                    {this.state.catalog.datasets.length > 0 ? this.state.catalog.datasets.map((item, index) => {
                            return <StudyListItem key={index}
                                                  newWindow={embedded}
                                                  link={item.relative_url}
                                                  logo={item.logo}
                                                  name={localizedText(item.title, 'en')}
                                                  description={localizedText(item.shortDescription, 'en')}
                                                  recruitmentStatus={item.recruitmentStatus}
                                                  intervention={item.intervention}
                                                  condition={item.condition}
                            />
                        },
                    ) : <div className="NoResults">No datasets found.</div>}
                </Col>
                {/*<Col md={4}>*/}
                {/*    {this.state.catalog.language && <MetadataItem label="Language" url={this.state.catalog.language.url}*/}
                {/*                                                  value={this.state.catalog.language.name}/>}*/}
                {/*    {this.state.catalog.homepage &&*/}
                {/*    <MetadataItem label="Homepage" value={this.state.catalog.homepage}/>}*/}
                {/*</Col>*/}
            </Row>
        </FAIRDataInformation>;

        {/*{this.state.catalog.publishers.length > 0 && <div className="Publishers">*/
        }
        {/*    {this.state.catalog.publishers.map((item, index) => {*/
        }
        {/*            return <Contact key={index}*/
        }
        {/*                            url={item.url}*/
        }
        {/*                            type={item.type}*/
        }
        {/*                            name={item.name}/>*/
        }
        {/*        }*/
        }
        {/*    )}*/
        }
        {/*</div>}*/
        }
    }
}
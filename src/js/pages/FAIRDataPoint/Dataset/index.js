import React, {Component} from "react";
import axios from "axios/index";

import {Col, Row} from "react-bootstrap";
import LoadingScreen from "../../../components/LoadingScreen";
import {localizedText} from "../../../util";
import MetadataItem from "../../../components/MetadataItem";
import ListItem from "../../../components/ListItem";
import queryString from "query-string";
import FAIRDataInformation from "../../../components/FAIRDataInformation";
import {RecruitmentStatus, StudyType} from "../../../components/MetadataItem/EnumMappings";
import Tags from "../../../components/Tags";
import Contacts from "../../../components/MetadataItem/Contacts";
import Organizations from "../../../components/MetadataItem/Organizations";

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
                distributions: [],
                logo: ''
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
                homepage: '',
                logo: ''
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
        const params = queryString.parse(this.props.location.search);
        const embedded = (typeof params.embed !== 'undefined');

        if(this.state.isLoading)
        {
            return <LoadingScreen showLoading={true}/>;
        }

        let description = null;
        let tags = [];
        let badge = (this.state.dataset.recruitmentStatus ? RecruitmentStatus[this.state.dataset.recruitmentStatus] : null);

        if(this.state.dataset.description !== null)
        {
            description = this.state.dataset.description;
        }
        else if(this.state.dataset.shortDescription !== null)
        {
            description = this.state.dataset.shortDescription;
        }

        if(this.state.dataset.condition !== null)
        {
            tags.push(this.state.dataset.condition.text);
        }
        if(this.state.dataset.intervention !== null)
        {
            tags.push(this.state.dataset.intervention.text);
        }


        return <FAIRDataInformation
            embedded={embedded}
            className="Dataset"
            title={localizedText(this.state.dataset.title, 'en')}
            version={this.state.dataset.version}
            issued={this.state.dataset.issued}
            modified={this.state.dataset.modified}
            license={this.state.dataset.license}
            badge={badge}
        >
            <Row>
                <Col md={8}>
                    {tags.length > 0 && <div className="StudyTags"><Tags tags={tags} /></div>}
                    {this.state.dataset.contactPoints.length > 0 && <Contacts contacts={this.state.dataset.contactPoints} />}
                    {description && <div className="InformationDescription">{localizedText(description, 'en', true)}</div>}

                    {this.state.dataset.distributions.length > 0 && <div>
                    <h2>Distributions</h2>
                    <div className="Description">
                        Distributions represent a specific available form of a dataset. Each dataset might be available in different forms, these forms might represent different formats of the dataset or different endpoints.
                    </div>
                    {this.state.dataset.distributions.map((item, index) => {
                        return <ListItem key={index}
                                         newWindow={embedded}
                                         link={item.relative_url}
                                         title={localizedText(item.title, 'en')}
                                         description={localizedText(item.description, 'en')}
                                         smallIcon={(item.accessRights === 2 || item.accessRights === 3) && 'lock'}
                        />}
                    )}
                </div>}
                </Col>
                <Col md={4}>
                    {this.state.dataset.logo && <div className="InformationLogo">
                        <img src={this.state.dataset.logo} alt={'Logo'}/>
                    </div>}
                    {this.state.dataset.studyType && <MetadataItem label="Type" value={StudyType[this.state.dataset.studyType]} />}
                    {this.state.dataset.estimatedEnrollment && <MetadataItem label="Estimated Enrollment" value={this.state.dataset.estimatedEnrollment} />}
                    {this.state.dataset.organizations.length > 0 && <Organizations organizations={this.state.dataset.organizations} />}

                    {/*{this.state.dataset.language && <MetadataItem label="Language" url={this.state.dataset.language.url} value={this.state.dataset.language.name} />}*/}
                    {this.state.dataset.landingpage && <MetadataItem label="Landing page" value={this.state.dataset.landingpage} />}
                </Col>
            </Row>
        </FAIRDataInformation>;

        // {this.state.catalog.publishers.length > 0 && <div className="Publishers">
        //     {this.state.dataset.publishers.map((item, index) => {
        //         return <Contact key={index}
        //                         url={item.url}
        //                         type={item.type}
        //                         name={item.name} />}
        //     )}
        // </div>}
    }
}

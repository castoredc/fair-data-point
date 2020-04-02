import React, {Component} from "react";
import axios from "axios/index";

import {Col, Row} from "react-bootstrap";
import LoadingScreen from "../../../components/LoadingScreen";
import {localizedText} from "../../../util";
import MetadataItem from "../../../components/MetadataItem";
import ListItem from "../../../components/ListItem";
import queryString from "query-string";
import FAIRDataInformation from "../../../components/FAIRDataInformation";
import {MethodType, RecruitmentStatus, StudyType} from "../../../components/MetadataItem/EnumMappings";
import Tags from "../../../components/Tags";
import Contacts from "../../../components/MetadataItem/Contacts";
import Organizations from "../../../components/MetadataItem/Organizations";
import {toast} from "react-toastify";
import ToastContent from "../../../components/ToastContent";

export default class Dataset extends Component {
    constructor(props) {
        super(props);
        this.state = {
            isLoadingDataset:       true,
            hasLoadedDataset:       false,
            isLoadingDistributions: true,
            hasLoadedDistributions: false,
            dataset:                null,
            distributions:          []
        };
    }

    componentDidMount() {
        this.getDataset();
        this.getDistributions();
    }

    getDataset = () => {
        axios.get('/api/catalog/' + this.props.match.params.catalog + '/dataset/' + this.props.match.params.dataset)
            .then((response) => {
                this.setState({
                    dataset: response.data,
                    isLoadingDataset: false,
                    hasLoadedDataset: true
                });
            })
            .catch((error) => {
                this.setState({
                    isLoadingDataset: false
                });

                const message = (error.response && typeof error.response.data.message !== "undefined") ? error.response.data.message : 'An error occurred while loading the dataset';
                toast.error(<ToastContent type="error" message={message} />);
            });
    };

    getDistributions = () => {
        axios.get('/api/catalog/' + this.props.match.params.catalog + '/dataset/' + this.props.match.params.dataset + '/distribution')
            .then((response) => {
                this.setState({
                    distributions: response.data,
                    isLoadingDistributions: false,
                    hasLoadedDistributions: true
                });
            })
            .catch((error) => {
                this.setState({
                    isLoadingDistributions: false
                });

                const message = (error.response && typeof error.response.data.message !== "undefined") ? error.response.data.message : 'An error occurred while loading the distributions';
                toast.error(<ToastContent type="error" message={message} />);
            });
    };

    render() {
        const params = queryString.parse(this.props.location.search);
        const embedded = (typeof params.embed !== 'undefined');

        if(this.state.isLoadingDataset || this.state.isLoadingDistributions)
        {
            return <LoadingScreen showLoading={true}/>;
        }

        let description = null;
        let tags = [];
        let badge = (this.state.dataset.recruitmentStatus ? RecruitmentStatus[this.state.dataset.recruitmentStatus] : null);

        if(this.state.dataset.description !== null && this.state.dataset.description.text !== '')
        {
            description = this.state.dataset.description;
        }
        else if(this.state.dataset.shortDescription !== null)
        {
            description = this.state.dataset.shortDescription;
        }

        if(this.state.dataset.condition !== null && this.state.dataset.condition.text !== '')
        {
            tags.push(this.state.dataset.condition.text);
        }
        if(this.state.dataset.intervention !== null && this.state.dataset.intervention.text !== '')
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
                <Col md={8} className="InformationCol">
                    {tags.length > 0 && <div className="StudyTags"><Tags tags={tags} /></div>}
                    {this.state.dataset.contactPoints.length > 0 && <Contacts contacts={this.state.dataset.contactPoints} />}
                    {description && <div className="InformationDescription">{localizedText(description, 'en', true)}</div>}

                    {this.state.distributions.length > 0 && <div>
                    <h2>Distributions</h2>
                    <div className="Description">
                        Distributions represent a specific available form of a dataset. Each dataset might be available in different forms, these forms might represent different formats of the dataset or different endpoints.
                    </div>
                    {this.state.distributions.map((item, index) => {
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
                    {this.state.dataset.methodType && <MetadataItem label="Method" value={MethodType[this.state.dataset.methodType]} />}
                    {this.state.dataset.estimatedEnrollment && <MetadataItem label="Estimated Enrollment" value={this.state.dataset.estimatedEnrollment} />}
                    {this.state.dataset.organizations.length > 0 && <Organizations organizations={this.state.dataset.organizations} />}

                    {/*{this.state.dataset.language && <MetadataItem label="Language" url={this.state.dataset.language.url} value={this.state.dataset.language.name} />}*/}
                    {this.state.dataset.landingPage && <MetadataItem label="Landing page" value={this.state.dataset.landingPage} />}
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

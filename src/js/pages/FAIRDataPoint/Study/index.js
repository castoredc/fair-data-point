import React, {Component} from "react";
import axios from "axios/index";

import {Col, Row} from "react-bootstrap";
import LoadingScreen from "../../../components/LoadingScreen";
import {paragraphText} from "../../../util";
import MetadataItem from "../../../components/MetadataItem";
import queryString from "query-string";
import FAIRDataInformation from "../../../components/FAIRDataInformation";
import {MethodType, RecruitmentStatus, StudyType} from "../../../components/MetadataItem/EnumMappings";
import Tags from "../../../components/Tags";
import Contacts from "../../../components/MetadataItem/Contacts";
import Organizations from "../../../components/MetadataItem/Organizations";
import {toast} from "react-toastify";
import ToastContent from "../../../components/ToastContent";
import InlineLoader from "../../../components/LoadingScreen/InlineLoader";
import StudyListItem from "../../../components/ListItem/StudyListItem";
import DatasetListItem from "../../../components/ListItem/DatasetListItem";
import DatasetList from "../../../components/List/DatasetList";

export default class Study extends Component {
    constructor(props) {
        super(props);
        this.state = {
            isLoadingStudy:       true,
            study:                null,
            datasets:             [],
            isLoadingDatasets:    true
        };
    }

    componentDidMount() {
        this.getStudy();
    }

    getStudy = () => {
        const { match } = this.props;

        axios.get('/api/study/slug/' + match.params.study)
            .then((response) => {
                this.setState({
                    study: response.data,
                    isLoadingStudy: false,
                });
            })
            .catch((error) => {
                this.setState({
                    isLoadingStudy: false
                });

                const message = (error.response && typeof error.response.data.error !== "undefined") ? error.response.data.error : 'An error occurred while loading the study';
                toast.error(<ToastContent type="error" message={message} />);
            });
    };

    render() {
        const { isLoadingStudy, study, isLoadingDatasets, datasets } = this.state;
        const { location } = this.props;
        
        const params = queryString.parse(location.search);
        const embedded = (typeof params.embed !== 'undefined');

        if(isLoadingStudy)
        {
            return <LoadingScreen showLoading={true}/>;
        }

        let tags = [];
        let badge = (study.metadata.recruitmentStatus ? RecruitmentStatus[study.metadata.recruitmentStatus] : null);

        if(study.metadata.condition !== null && study.metadata.condition !== '')
        {
            tags.push(study.metadata.condition);
        }
        if(study.metadata.intervention !== null && study.metadata.intervention !== '')
        {
            tags.push(study.metadata.intervention);
        }

        const fdp = (location.state && 'fdp' in location.state) ? location.state.fdp : null;
        const catalog = (location.state && 'catalog' in location.state) ? location.state.catalog : null;
        const breadcrumbs = {fdp: fdp, catalog: catalog, study: study};

        return <FAIRDataInformation
            embedded={embedded}
            className="Dataset"
            title={study.metadata.briefName}
            badge={badge}
            breadcrumbs={breadcrumbs}
        >
            <Row>
                <Col md={8} className="InformationCol">
                    {tags.length > 0 && <div className="StudyTags"><Tags tags={tags} /></div>}
                    {study.metadata.contacts.length > 0 && <Contacts contacts={study.metadata.contacts} />}
                    {study.metadata.briefSummary && <div className="InformationDescription">{paragraphText(study.metadata.briefSummary)}</div>}

                    <DatasetList study={study} fdp={fdp} catalog={catalog} />
                </Col>
                <Col md={4}>
                    {study.metadata.logo && <div className="InformationLogo">
                        <img src={study.metadata.logo} alt={'Logo'}/>
                    </div>}
                    {study.metadata.studyType && <MetadataItem label="Type" value={StudyType[study.metadata.studyType]} />}
                    {study.metadata.methodType && <MetadataItem label="Method" value={MethodType[study.metadata.methodType]} />}
                    {study.metadata.estimatedEnrollment && <MetadataItem label="Estimated Enrollment" value={study.metadata.estimatedEnrollment} />}
                    {study.metadata.organizations.length > 0 && <Organizations organizations={study.metadata.organizations} />}

                    {/*{study.language && <MetadataItem label="Language" url={study.language.url} value={study.language.name} />}*/}
                    {/*{study.landingPage && <MetadataItem label="Landing page" value={study.landingPage} />}*/}
                </Col>
            </Row>
        </FAIRDataInformation>;
    }
}

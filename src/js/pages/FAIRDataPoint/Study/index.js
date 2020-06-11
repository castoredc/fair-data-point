import React, {Component} from "react";
import axios from "axios/index";

import {Col, Row} from "react-bootstrap";
import LoadingScreen from "../../../components/LoadingScreen";
import {localizedText, paragraphText} from "../../../util";
import MetadataItem from "../../../components/MetadataItem";
import queryString from "query-string";
import FAIRDataInformation from "../../../components/FAIRDataInformation";
import {MethodType, RecruitmentStatus, StudyType} from "../../../components/MetadataItem/EnumMappings";
import Tags from "../../../components/Tags";
import Contacts from "../../../components/MetadataItem/Contacts";
import Organizations from "../../../components/MetadataItem/Organizations";
import {toast} from "react-toastify";
import ToastContent from "../../../components/ToastContent";

export default class Study extends Component {
    constructor(props) {
        super(props);
        this.state = {
            isLoadingStudy:       true,
            hasLoadedStudy:       false,
            study:                null,
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
                    hasLoadedStudy: true
                });
            })
            .catch((error) => {
                this.setState({
                    isLoadingStudy: false
                });

                const message = (error.response && typeof error.response.data.message !== "undefined") ? error.response.data.message : 'An error occurred while loading the study';
                toast.error(<ToastContent type="error" message={message} />);
            });
    };

    render() {
        const { isLoadingStudy, study } = this.state;
        
        const params = queryString.parse(this.props.location.search);
        const embedded = (typeof params.embed !== 'undefined');

        if(this.state.isLoadingStudy)
        {
            return <LoadingScreen showLoading={true}/>;
        }

        let tags = [];
        let badge = (study.recruitmentStatus ? RecruitmentStatus[study.recruitmentStatus] : null);

        if(study.condition !== null && study.condition !== '')
        {
            tags.push(study.condition);
        }
        if(study.intervention !== null && study.intervention !== '')
        {
            tags.push(study.intervention);
        }


        return <FAIRDataInformation
            embedded={embedded}
            className="Dataset"
            title={study.briefName}
            badge={badge}
        >
            <Row>
                <Col md={8} className="InformationCol">
                    {tags.length > 0 && <div className="StudyTags"><Tags tags={tags} /></div>}
                    {study.contacts.length > 0 && <Contacts contacts={study.contacts} />}
                    {study.briefSummary && <div className="InformationDescription">{paragraphText(study.briefSummary)}</div>}
                </Col>
                <Col md={4}>
                    {study.logo && <div className="InformationLogo">
                        <img src={study.logo} alt={'Logo'}/>
                    </div>}
                    {study.studyType && <MetadataItem label="Type" value={StudyType[study.studyType]} />}
                    {study.methodType && <MetadataItem label="Method" value={MethodType[study.methodType]} />}
                    {study.estimatedEnrollment && <MetadataItem label="Estimated Enrollment" value={study.estimatedEnrollment} />}
                    {study.organizations.length > 0 && <Organizations organizations={study.organizations} />}

                    {/*{study.language && <MetadataItem label="Language" url={study.language.url} value={study.language.name} />}*/}
                    {/*{study.landingPage && <MetadataItem label="Landing page" value={study.landingPage} />}*/}
                </Col>
            </Row>
        </FAIRDataInformation>;
    }
}

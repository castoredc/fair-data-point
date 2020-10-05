import React, {Component} from "react";
import axios from "axios";
import {paragraphText} from "../../../util";
import MetadataItem from "../../../components/MetadataItem";
import Header from "../../../components/Layout/Header";
import {MethodType, RecruitmentStatus, StudyType} from "../../../components/MetadataItem/EnumMappings";
import Tags from "../../../components/Tags";
import Contacts from "../../../components/MetadataItem/Contacts";
import Organizations from "../../../components/MetadataItem/Organizations";
import {toast} from "react-toastify";
import ToastContent from "../../../components/ToastContent";
import DatasetList from "../../../components/List/DatasetList";
import Layout from "../../../components/Layout";
import MainBody from "../../../components/Layout/MainBody";
import {getBreadCrumbs} from "../../../utils/BreadcrumbUtils";

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
        const { isLoadingStudy, study } = this.state;
        const { location, user, embedded } = this.props;

        const breadcrumbs = getBreadCrumbs(location, {study});

        let tags = [];
        let badge = (study && study.metadata.recruitmentStatus ? RecruitmentStatus[study.metadata.recruitmentStatus] : null);

        if(study && study.metadata.condition !== null && study.metadata.condition !== '')
        {
            tags.push(study.metadata.condition);
        }
        if(study && study.metadata.intervention !== null && study.metadata.intervention !== '')
        {
            tags.push(study.metadata.intervention);
        }

        const title = study ? study.metadata.briefName : null;

        return <Layout
            className="Study"
            title={title}
            isLoading={isLoadingStudy}
            embedded={embedded}
        >
            <Header user={user} embedded={embedded} breadcrumbs={breadcrumbs} title={title} badge={badge} />

            <MainBody isLoading={isLoadingStudy}>
                {study && <>
                    <div className="MainCol">
                        {study.metadata.contacts.length > 0 && <Contacts contacts={study.metadata.contacts} />}

                        {(study.metadata.briefSummary || tags.length > 0) && <div className="InformationDescription">
                            {study.metadata.briefSummary && <div>{paragraphText(study.metadata.briefSummary)}</div>}
                            {tags.length > 0 && <div className="StudyTags"><Tags tags={tags} /></div>}
                        </div>}

                        <DatasetList study={study} state={breadcrumbs.current ? breadcrumbs.current.state : null} />
                    </div>
                    <div className="SideCol">
                        {study.metadata.logo && <div className="InformationLogo">
                            <img src={study.metadata.logo} alt={'Logo'}/>
                        </div>}
                        {study.metadata.studyType && <MetadataItem label="Type" value={StudyType[study.metadata.studyType]} />}
                        {study.metadata.methodType && <MetadataItem label="Method" value={MethodType[study.metadata.methodType]} />}
                        {study.metadata.estimatedEnrollment && <MetadataItem label="Estimated Enrollment" value={study.metadata.estimatedEnrollment} />}
                        {study.metadata.organizations.length > 0 && <Organizations organizations={study.metadata.organizations} />}

                        {/*{study.language && <MetadataItem label="Language" url={study.language.url} value={study.language.name} />}*/}
                        {/*{study.landingPage && <MetadataItem label="Landing page" value={study.landingPage} />}*/}
                    </div>
                </>}
            </MainBody>
        </Layout>;
    }
}

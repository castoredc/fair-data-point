import React, { Component } from 'react';
import { paragraphText } from '../../../util';
import Header from '../../../components/Layout/Header';
import { RecruitmentStatus } from '../../../components/MetadataItem/EnumMappings';
import Tags from '../../../components/Tags';
import Contacts from '../../../components/MetadataItem/Contacts';
import { toast } from 'react-toastify';
import ToastItem from 'components/ToastItem';
import DatasetList from '../../../components/List/DatasetList';
import Layout from '../../../components/Layout';
import MainBody from '../../../components/Layout/MainBody';
import { getBreadCrumbs } from '../../../utils/BreadcrumbUtils';
import AssociatedItemsBar from '../../../components/AssociatedItemsBar';
import LegacyMetadataSideBar from '../../../components/MetadataSideBar/LegacyMetadataSideBar';
import { apiClient } from 'src/js/network';

export default class Study extends Component {
    constructor(props) {
        super(props);
        this.state = {
            isLoadingStudy: true,
            study: null,
            datasets: [],
            isLoadingDatasets: true,
        };
    }

    componentDidMount() {
        this.getStudy();
    }

    getStudy = () => {
        const { match } = this.props;

        apiClient
            .get('/api/study/slug/' + match.params.study)
            .then(response => {
                this.setState({
                    study: response.data,
                    isLoadingStudy: false,
                });
            })
            .catch(error => {
                this.setState({
                    isLoadingStudy: false,
                });

                const message =
                    error.response && typeof error.response.data.error !== 'undefined'
                        ? error.response.data.error
                        : 'An error occurred while loading the study';
                toast.error(<ToastItem type="error" title={message} />);
            });
    };

    render() {
        const { isLoadingStudy, study } = this.state;
        const { location, user, embedded } = this.props;

        const breadcrumbs = getBreadCrumbs(location, { study });

        let tags = [];
        let badge = study && study.metadata.recruitmentStatus ? RecruitmentStatus[study.metadata.recruitmentStatus] : null;

        if (study && study.metadata.condition !== null && study.metadata.condition !== '') {
            tags.push(study.metadata.condition);
        }
        if (study && study.metadata.intervention !== null && study.metadata.intervention !== '') {
            tags.push(study.metadata.intervention);
        }

        const title = study ? study.metadata.briefName : null;

        return (
            <Layout className="Study" title={title} isLoading={isLoadingStudy} embedded={embedded}>
                <Header user={user} embedded={embedded} breadcrumbs={breadcrumbs} title={title} badge={badge} />

                <MainBody isLoading={isLoadingStudy}>
                    {study && (
                        <>
                            <div className="MainCol">
                                {study.metadata.contacts.length > 0 && <Contacts contacts={study.metadata.contacts} />}

                                {(study.metadata.briefSummary || tags.length > 0) && (
                                    <div className="InformationDescription">
                                        {study.metadata.briefSummary && <div>{paragraphText(study.metadata.briefSummary)}</div>}
                                        {tags.length > 0 && (
                                            <div className="StudyTags">
                                                <Tags tags={tags} />
                                            </div>
                                        )}
                                    </div>
                                )}

                                <AssociatedItemsBar items={study.count} current="dataset" />

                                <DatasetList study={study} state={breadcrumbs.current ? breadcrumbs.current.state : null} />
                            </div>
                            <div className="SideCol">
                                <LegacyMetadataSideBar type="study" metadata={study.metadata} name={title} />
                            </div>
                        </>
                    )}
                </MainBody>
            </Layout>
        );
    }
}

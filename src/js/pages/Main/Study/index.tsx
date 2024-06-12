import React from 'react';
import { paragraphText } from '../../../util';
import Header from '../../../components/Layout/Header';
import { RecruitmentStatus } from '../../../components/MetadataItem/EnumMappings';
import Tags from '../../../components/Tags';
import Contacts from '../../../components/MetadataItem/Contacts';
import DatasetList from '../../../components/List/DatasetList';
import Layout from '../../../components/Layout';
import MainBody from '../../../components/Layout/MainBody';
import { getBreadCrumbs } from '../../../utils/BreadcrumbUtils';
import AssociatedItemsBar from '../../../components/AssociatedItemsBar';
import { AuthorizedRouteComponentProps } from 'components/Route';
import useGetStudy from '../../../hooks/useGetStudy';

interface StudyProps extends AuthorizedRouteComponentProps {
    embedded: boolean;
}

const Study: React.FC<StudyProps> = ({ user, embedded, location, match }) => {
    const { isLoading: isLoadingStudy, study } = useGetStudy(match.params.study);

    const breadcrumbs = getBreadCrumbs(location, { study });

    let tags: any[] = [];
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
                                    {study.metadata.briefSummary &&
                                        <div>{paragraphText(study.metadata.briefSummary)}</div>}
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
                        </div>
                    </>
                )}
            </MainBody>
        </Layout>
    );
};

export default Study;
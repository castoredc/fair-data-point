import React from 'react';
import { localizedText, paragraphText } from '../../../util';
import Header from '../../../components/Layout/Header';
import DatasetList from '../../../components/List/DatasetList';
import Layout from '../../../components/Layout';
import MainBody from '../../../components/Layout/MainBody';
import { getBreadCrumbs } from '../../../utils/BreadcrumbUtils';
import AssociatedItemsBar from '../../../components/AssociatedItemsBar';
import { AuthorizedRouteComponentProps } from 'components/Route';
import useGetStudy from '../../../hooks/useGetStudy';
import MetadataDescription from 'components/MetadataSideBar/MetadataDescription';
import MetadataSideBar from 'components/MetadataSideBar';

interface StudyProps extends AuthorizedRouteComponentProps {
    embedded: boolean;
}

const Study: React.FC<StudyProps> = ({ user, embedded, location, match }) => {
    const { isLoading: isLoadingStudy, study } = useGetStudy(match.params.study);

    const breadcrumbs = getBreadCrumbs(location, { study });
    const title = study ? localizedText(study.metadata.title, 'en') : null;

    return (
        <Layout className="Study" embedded={embedded}>
            <Header user={user} embedded={embedded} breadcrumbs={breadcrumbs} title={title} />

            <MainBody isLoading={isLoadingStudy}>
                {study && (
                    <>
                        <div className="MainCol">
                            <MetadataDescription metadata={study.metadata} />
                        </div>
                        <div className="SideCol">
                            <MetadataSideBar
                                metadata={study.metadata}
                                title={title}
                            />
                        </div>

                        <AssociatedItemsBar items={study.count} current="dataset" />

                        <DatasetList
                            study={study}
                            state={breadcrumbs.current ? breadcrumbs.current.state : null}
                            className="MainCol"
                        />
                    </>
                )}
            </MainBody>
        </Layout>
    );
};

export default Study;
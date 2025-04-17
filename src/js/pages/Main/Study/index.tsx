import React from 'react';
import { localizedText } from '../../../util';
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
import Grid from '@mui/material/Grid';

interface StudyProps extends AuthorizedRouteComponentProps {
    embedded: boolean;
}

const Study: React.FC<StudyProps> = ({ user, embedded, location, match }) => {
    const { isLoading: isLoadingStudy, study } = useGetStudy(match.params.study);

    const breadcrumbs = getBreadCrumbs(location, { study });
    let title = study ? localizedText(study.metadata.title, 'en') : 'Untitled study';

    if (title === '') {
        title = 'Untitled study';
    }

    return (
        <Layout embedded={embedded}>
            <Header user={user} embedded={embedded} breadcrumbs={breadcrumbs} title={title} />

            <MainBody isLoading={isLoadingStudy}>
                {study && (
                    <>
                        <Grid container spacing={2}>
                            <Grid size={8}>
                                <MetadataDescription metadata={study.metadata} />
                            </Grid>
                            <Grid size={4}>
                                <MetadataSideBar metadata={study.metadata} title={title} />
                            </Grid>
                        </Grid>

                        <AssociatedItemsBar items={study.count} current="dataset" />

                        <DatasetList study={study} state={breadcrumbs.current ? breadcrumbs.current.state : null} />
                    </>
                )}
            </MainBody>
        </Layout>
    );
};

export default Study;
